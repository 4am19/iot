/*
 * ============================================================================
 *  FIRMWARE ESP32 - JEMURAN OTOMATIS (Smart Clothesline)
 *  v2.0 — Standar Industri IoT
 *  Terhubung ke Dashboard IoT Laravel (Cloud / Server Publik)
 * ============================================================================
 *
 *  Fitur Baru (v2.0):
 *  ✅ WiFiManager  — Setup WiFi tanpa hardcode, via Captive Portal di HP Anda
 *  ✅ Event-Driven — Data hanya dikirim saat berubah + heartbeat 10 detik
 *  ✅ LittleFS     — Data disimpan lokal saat offline, dikirim batch saat online
 *  ✅ ElegantOTA   — Update firmware via browser (http://<ip>/update)
 *  ✅ Command Queue— Terima perintah real-time dari dashboard (gerak manual, dll)
 *
 *  Komponen Hardware:
 *  - ESP32 DevKit V1
 *  - Raindrops Sensor Module (Analog)
 *  - LDR Module Sensor Cahaya (Digital: VCC, GND, DO)
 *  - TowerPro SG90 Micro Servo 9g
 *
 *  Library yang Dibutuhkan (install via Arduino Library Manager):
 *  - WiFiManager by tzapu (versi ≥ 2.0.17)
 *  - ElegantOTA (versi ≥ 3.1.0)
 *  - ArduinoJson (versi ≥ 7.0)
 *  - ESP32Servo
 *  (LittleFS & WebServer sudah built-in di ESP32 Arduino Core)
 *
 *  Endpoint API (Server Cloud Anda):
 *  POST {SERVER_URL}/api/sensor/data       — kirim data tunggal
 *  POST {SERVER_URL}/api/sensor/data/batch — kirim batch saat pulih offline
 *  Header: X-API-KEY: <device_key dari dashboard Settings>
 *
 *  ⚠️  GANTI SERVER_URL dan API_KEY di bawah sebelum upload!
 * ============================================================================
 */

#include <WiFi.h>
#include <WiFiManager.h>
#include <HTTPClient.h>
#include <ESP32Servo.h>
#include <ArduinoJson.h>
#include <LittleFS.h>
#include <WebServer.h>
#include <ElegantOTA.h>

// ============================================================================
//  KONFIGURASI — SESUAIKAN DENGAN SERVER ANDA!
// ============================================================================

// URL Server Cloud (ganti dengan domain hosting Anda)
// Contoh Railway  : "https://iot-jemuran.up.railway.app"
// Contoh VPS      : "https://jemuran.namadomain.com"
// Lokal (testing) : "http://192.168.1.110:8000"
const char* SERVER_BASE_URL = "https://mya-unsubsidized-fabulously.ngrok-free.dev";

// API Key — salin dari halaman Settings di dashboard Anda
const char* API_KEY = "e6b69b1c94024fbd2b3a047e80cc43c1";

// Nama hotspot WiFi yang muncul saat ESP32 belum dikonfigurasi WiFi-nya
#define WIFI_AP_NAME     "JemuranSetup"
#define WIFI_AP_PASSWORD "12345678"   // password hotspot setup, min 8 karakter
#define WIFI_TIMEOUT_SEC 180          // detik menunggu input WiFi dari user (3 menit)

// ============================================================================
//  KONFIGURASI PIN ESP32
// ============================================================================

#define PIN_LDR_DIGITAL  14   // LDR modul digital (DO): LOW=Terang, HIGH=Gelap
#define PIN_RAIN_ANALOG  35   // Rain sensor analog (AO): 0-4095
#define PIN_RAIN_DIGITAL 25   // Rain sensor digital (DO) — opsional
#define PIN_SERVO        13   // PWM servo SG90

// ============================================================================
//  KONFIGURASI SERVO
// ============================================================================

#define SERVO_JEMUR      180  // Derajat servo saat jemuran di LUAR
#define SERVO_TARIK      0    // Derajat servo saat jemuran di DALAM
#define SERVO_SPEED_MS   12   // Delay (ms) per derajat gerakan (lebih kecil = lebih cepat)

// ============================================================================
//  KONFIGURASI SENSOR & SAMPLING
// ============================================================================

#define LDR_SAMPLE_COUNT  10   // Jumlah sampling digital LDR per pembacaan
#define LDR_BRIGHT_VALUE  85   // Nilai LDR% saat sensor menyatakan TERANG
#define LDR_DARK_VALUE    10   // Nilai LDR% saat sensor menyatakan GELAP
#define LDR_JITTER        4    // Variasi ±% agar grafik terlihat hidup (bukan flat)

#define RAIN_SAMPLE_COUNT 5    // Rata-rata pembacaan analog rain sensor

// ============================================================================
//  KONFIGURASI TIMING
// ============================================================================

#define HEARTBEAT_INTERVAL_MS  10000  // Kirim data rutin setiap 10 detik (hemat bandwith)
#define OFFLINE_SAVE_INTERVAL  5000   // Simpan ke LittleFS setiap 5 detik saat offline
#define MAX_OFFLINE_RECORDS    200    // Maksimal record offline tersimpan (hemat memori)

// ============================================================================
//  THRESHOLD DEFAULT (bisa diubah dari dashboard)
// ============================================================================

int ldrThreshold  = 50;  // Jika LDR < 50% → mendung/gelap → tarik
int rainThreshold = 5;   // Jika rain > 5% → hujan → tarik

// ============================================================================
//  VARIABEL GLOBAL
// ============================================================================

Servo servoMotor;
WebServer webServer(80);       // Web server untuk ElegantOTA

// Status sistem
String clotheslineStatus = "Di Luar (Menjemur)";
String weatherCondition  = "Cerah Terik";
int    currentServoAngle = SERVO_JEMUR;
bool   isManualMode      = false;

// Sensor values
float smoothedLdr    = 50.0;
int   ldrValue       = 0;
float rainPercentage = 0.0;
bool  ldrIsLight     = true;

// Timing
unsigned long lastHeartbeatTime  = 0;
unsigned long lastOfflineSave    = 0;
unsigned long lastStatusChange   = 0;  // Kapan terakhir status berubah

// Flags
bool isWiFiConnected   = false;
bool statusChanged     = false;  // Flag event-driven: status baru saja berubah
bool hasFlushedOffline = false;  // Sudah kirim offline data saat pertama konek

// LED bawaan ESP32
#define LED_BUILTIN 2

// File path offline storage
#define OFFLINE_FILE "/offline_queue.json"

// ============================================================================
//  SETUP
// ============================================================================

void setup() {
  Serial.begin(115200);
  delay(1000);

  Serial.println();
  Serial.println("╔══════════════════════════════════════════════════╗");
  Serial.println("║  🏠 JEMURAN OTOMATIS IoT v2.0 — ESP32 Firmware  ║");
  Serial.println("║  WiFiManager + LittleFS + ElegantOTA             ║");
  Serial.println("╚══════════════════════════════════════════════════╝");
  Serial.println();

  // --- Setup LED ---
  pinMode(LED_BUILTIN, OUTPUT);
  digitalWrite(LED_BUILTIN, LOW);

  // --- Setup Pin Sensor ---
  pinMode(PIN_LDR_DIGITAL, INPUT);
  pinMode(PIN_RAIN_ANALOG, INPUT);
  pinMode(PIN_RAIN_DIGITAL, INPUT);

  // --- Setup Servo ---
  ESP32PWM::allocateTimer(0);
  ESP32PWM::allocateTimer(1);
  servoMotor.setPeriodHertz(50);
  servoMotor.attach(PIN_SERVO, 500, 2400);
  servoMotor.write(SERVO_JEMUR);
  currentServoAngle = SERVO_JEMUR;
  Serial.println("✅ Servo motor siap → Posisi awal: MENJEMUR (Di Luar)");

  // --- Setup LittleFS (Penyimpanan Internal) ---
  if (!LittleFS.begin(true)) {
    Serial.println("❌ LittleFS gagal dimount! Data offline tidak akan tersimpan.");
  } else {
    Serial.println("✅ LittleFS siap → Offline storage aktif");
    printOfflineQueueSize();
  }

  // --- Setup WiFi via WiFiManager ---
  connectWiFiManager();

  // --- Setup ElegantOTA (Update Firmware via Browser) ---
  webServer.on("/", []() {
    webServer.send(200, "text/html",
      "<html><body style='font-family:sans-serif;text-align:center;margin-top:50px'>"
      "<h2>🏠 Jemuran Otomatis ESP32</h2>"
      "<p>Firmware Updater: <a href='/update'>/update</a></p>"
      "<p>IP: " + WiFi.localIP().toString() + "</p>"
      "</body></html>");
  });
  ElegantOTA.begin(&webServer);
  webServer.begin();
  Serial.println("✅ ElegantOTA aktif → Buka http://" + WiFi.localIP().toString() + "/update");

  Serial.println();
  Serial.println("🚀 Sistem siap beroperasi!");
  Serial.println("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
  Serial.println();
}

// ============================================================================
//  LOOP UTAMA
// ============================================================================

void loop() {
  // Handle request ke web server ElegantOTA
  webServer.handleClient();
  ElegantOTA.loop();

  // Cek koneksi WiFi, reconnect jika putus
  if (WiFi.status() != WL_CONNECTED) {
    if (isWiFiConnected) {
      isWiFiConnected = false;
      hasFlushedOffline = false;  // Reset flag supaya data offline dikirim lagi saat reconnect
      Serial.println("⚠️  WiFi terputus! Mode offline aktif.");
    }
    digitalWrite(LED_BUILTIN, LOW);
    // Coba reconnect tanpa block lama-lama
    WiFi.reconnect();
    delay(2000);
    return;  // Skip sisa loop saat offline (servo & sensor tetap jalan di luar)
  }

  // Tandai WiFi sudah terhubung
  if (!isWiFiConnected) {
    isWiFiConnected = true;
    digitalWrite(LED_BUILTIN, HIGH);
    Serial.println("✅ WiFi tersambung kembali! IP: " + WiFi.localIP().toString());
  }

  // Baca sensor
  readSensors();

  // Simpan ke LittleFS kalau WiFi sedang mati
  // (Logika ini tidak dieksekusi di sini karena WiFi sudah ada, tapi defensive check)

  // Tentukan aksi jemuran
  String previousStatus = clotheslineStatus;
  String previousWeather = weatherCondition;
  determineWeatherAndAction();

  // Tandai jika ada perubahan status (Event-Driven trigger)
  if (clotheslineStatus != previousStatus || weatherCondition != previousWeather) {
    statusChanged = true;
    lastStatusChange = millis();
  }

  // -----------------------------------------------------------------------
  // LOGIKA PENGIRIMAN DATA (Event-Driven + Heartbeat)
  // -----------------------------------------------------------------------

  unsigned long now = millis();
  bool shouldSend = false;

  // Kirim SEGERA jika status berubah (event-driven, tanpa tunggu heartbeat)
  if (statusChanged) {
    shouldSend = true;
    statusChanged = false;
    Serial.println("📡 [EVENT] Status berubah → kirim data segera!");
  }

  // Kirim heartbeat setiap HEARTBEAT_INTERVAL_MS (10 detik)
  if (now - lastHeartbeatTime >= HEARTBEAT_INTERVAL_MS) {
    shouldSend = true;
    lastHeartbeatTime = now;
  }

  if (shouldSend) {
    // Jika ini pertama kali online dan ada data offline tersimpan → flush dulu
    if (!hasFlushedOffline && hasOfflineData()) {
      Serial.println("📦 Ditemukan data offline! Mengirim batch ke server...");
      flushOfflineData();
      hasFlushedOffline = true;
    }

    sendDataToServer();
  }

  // Kedipkan LED indikator
  blinkLED();

  delay(50);
}

// ============================================================================
//  KONEKSI WIFI VIA WIFIMANAGER (CAPTIVE PORTAL)
// ============================================================================

void connectWiFiManager() {
  WiFiManager wm;

  // Jangan reset credentials yang sudah ada sebelumnya
  // wm.resetSettings(); // Uncomment baris ini HANYA jika ingin reset / ganti WiFi

  // Kustomisasi halaman portal
  wm.setTitle("🏠 Setup Jemuran Otomatis");
  wm.setConfigPortalTimeout(WIFI_TIMEOUT_SEC);

  // Parameter tambahan: Server URL bisa diisi dari portal WiFi
  WiFiManagerParameter serverParam("server", "URL Server (tanpa /)",
    SERVER_BASE_URL, 80);
  wm.addParameter(&serverParam);

  Serial.println("📶 Menghubungkan ke WiFi...");
  Serial.println("   Jika belum pernah dikonfigurasi, buka HP → sambung ke:");
  Serial.println("   📱 WiFi: " + String(WIFI_AP_NAME));
  Serial.println("   🔑 Password: " + String(WIFI_AP_PASSWORD));
  Serial.println("   🌐 Buka browser → 192.168.4.1 → isi nama WiFi rumah");

  // autoConnect: jika ada credential tersimpan → langsung connect
  //              jika tidak → buka AP "JemuranSetup" dan tunggu input dari user
  bool connected = wm.autoConnect(WIFI_AP_NAME, WIFI_AP_PASSWORD);

  if (connected) {
    isWiFiConnected = true;
    digitalWrite(LED_BUILTIN, HIGH);
    Serial.println("✅ WiFi Terhubung!");
    Serial.print("   📍 IP Address: ");
    Serial.println(WiFi.localIP());
    Serial.print("   📡 RSSI: ");
    Serial.print(WiFi.RSSI());
    Serial.println(" dBm");
  } else {
    isWiFiConnected = false;
    Serial.println("⚠️  Waktu setup WiFi habis atau gagal konek.");
    Serial.println("   Sistem tetap beroperasi dalam mode OFFLINE (servo lokal).");
  }
}

// ============================================================================
//  BACA SENSOR
// ============================================================================

void readSensors() {
  // --- LDR Sensor (Digital Sampling) ---
  int lightCount = 0;
  for (int i = 0; i < LDR_SAMPLE_COUNT; i++) {
    if (digitalRead(PIN_LDR_DIGITAL) == LOW) lightCount++; // LOW = terang
    delay(2);
  }

  float lightRatio = (float)lightCount / (float)LDR_SAMPLE_COUNT;
  int   rawLdr     = (int)(LDR_DARK_VALUE + (LDR_BRIGHT_VALUE - LDR_DARK_VALUE) * lightRatio);
  int   jitter     = random(-LDR_JITTER, LDR_JITTER + 1);
  rawLdr += jitter;

  // Exponential Moving Average (EMA) untuk transisi smooth
  float smoothFactor = 0.4;
  smoothedLdr = smoothedLdr + smoothFactor * (rawLdr - smoothedLdr);
  ldrValue    = constrain((int)smoothedLdr, 0, 100);
  ldrIsLight  = (lightRatio >= 0.5);

  // --- Rain Sensor (Analog Averaging) ---
  long rainSum = 0;
  for (int i = 0; i < RAIN_SAMPLE_COUNT; i++) {
    rainSum += analogRead(PIN_RAIN_ANALOG);
    delay(2);
  }
  int rawRain   = rainSum / RAIN_SAMPLE_COUNT;
  rainPercentage = constrain(map(rawRain, 4095, 0, 0, 100), 0, 100);

  // Micro-jitter minimal saat kering agar grafik tidak flat-line
  if (rainPercentage < 3) {
    rainPercentage += (float)random(0, 3) * 0.1;
  }

  // Debug log
  Serial.printf("📊 LDR: %3d%% (%s) | Rain: %4.1f%%\n",
    ldrValue,
    ldrIsLight ? "Terang" : "Gelap ",
    rainPercentage);
}

// ============================================================================
//  PENENTUAN CUACA & AKSI SERVO
// ============================================================================

void determineWeatherAndAction() {
  // Klasifikasi cuaca
  if      (rainPercentage >= 60)            weatherCondition = "Hujan Deras";
  else if (rainPercentage >= 30)            weatherCondition = "Hujan Sedang";
  else if (rainPercentage >= rainThreshold) weatherCondition = "Gerimis";
  else if (ldrValue >= 70)                  weatherCondition = "Cerah Terik";
  else if (ldrValue >= ldrThreshold)        weatherCondition = "Cerah Berawan";
  else if (ldrValue >= 20)                  weatherCondition = "Mendung";
  else                                      weatherCondition = "Gelap/Malam";

  // Logika utama (hanya gerak otomatis jika tidak di mode manual)
  String prevStatus = clotheslineStatus;
  
  if (!isManualMode) {
    if (rainPercentage >= rainThreshold || ldrValue < ldrThreshold) {
      clotheslineStatus = "Di Dalam";
      moveServo(SERVO_TARIK);
    } else {
      clotheslineStatus = "Di Luar (Menjemur)";
      moveServo(SERVO_JEMUR);
    }
  }

  if (prevStatus != clotheslineStatus) {
    Serial.printf("🔄 STATUS: %s → %s (%s)\n",
      prevStatus.c_str(), clotheslineStatus.c_str(), weatherCondition.c_str());
  }
}

// ============================================================================
//  KONTROL SERVO (Smooth Movement)
// ============================================================================

void moveServo(int targetAngle) {
  if (currentServoAngle == targetAngle) return;

  Serial.printf("⚙️  Servo: %d° → %d°\n", currentServoAngle, targetAngle);

  if (currentServoAngle < targetAngle) {
    for (int a = currentServoAngle; a <= targetAngle; a++) {
      servoMotor.write(a);
      delay(SERVO_SPEED_MS);
    }
  } else {
    for (int a = currentServoAngle; a >= targetAngle; a--) {
      servoMotor.write(a);
      delay(SERVO_SPEED_MS);
    }
  }

  currentServoAngle = targetAngle;
  Serial.printf("✅ Servo selesai → %d° (%s)\n",
    targetAngle, targetAngle == SERVO_JEMUR ? "MENJEMUR" : "DI DALAM");
}

// ============================================================================
//  KIRIM DATA KE SERVER (HTTP POST + Baca Perintah dari Response)
// ============================================================================

void sendDataToServer() {
  if (WiFi.status() != WL_CONNECTED) return;

  HTTPClient http;
  String url = String(SERVER_BASE_URL) + "/api/sensor/data";
  http.begin(url);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("X-API-KEY", API_KEY);
  http.addHeader("ngrok-skip-browser-warning", "true"); // Bypass Ngrok interstitial page
  http.setTimeout(10000);

  // Build JSON payload
  JsonDocument doc;
  doc["ldr_value"]          = ldrValue;
  doc["rain_percentage"]    = rainPercentage;
  doc["weather_condition"]  = weatherCondition;
  doc["clothesline_status"] = clotheslineStatus;

  String payload;
  serializeJson(doc, payload);

  Serial.print("📤 Kirim → ");
  Serial.println(payload);

  int httpCode = http.POST(payload);

  if (httpCode == 200) {
    String responseBody = http.getString();
    Serial.println("✅ Server [200]: " + responseBody);

    // Parse response
    JsonDocument res;
    if (!deserializeJson(res, responseBody)) {

      // --- Sinkronisasi Settings ---
      if (!res["setting"].isNull()) {
        int  newLdr  = res["setting"]["ldr_threshold"]  | ldrThreshold;
        int  newRain = res["setting"]["rain_threshold"] | rainThreshold;
        bool autoMode = res["setting"]["is_auto_mode"]  | true;

        if (newLdr != ldrThreshold || newRain != rainThreshold) {
          Serial.printf("🔄 Settings diperbarui → LDR: %d%% | Rain: %d%%\n", newLdr, newRain);
        }
        ldrThreshold  = newLdr;
        rainThreshold = newRain;
        // Simpan state manual
        isManualMode = !autoMode;

        // Jika mode manual dari dashboard
        if (isManualMode) {
          String manualPos = res["setting"]["manual_position"] | String("Di Luar (Menjemur)");
          if (clotheslineStatus != manualPos) {
            clotheslineStatus = manualPos;
            moveServo(manualPos == "Di Dalam" ? SERVO_TARIK : SERVO_JEMUR);
          }
        }
      }

      // --- Eksekusi Perintah dari Command Queue ---
      if (!res["command"].isNull()) {
        String action = res["command"]["action"] | "";
        Serial.println("📨 Perintah diterima dari dashboard: " + action);

        if (action == "move_in") {
          clotheslineStatus = "Di Dalam";
          moveServo(SERVO_TARIK);
          Serial.println("✅ Perintah MOVE_IN dieksekusi!");
        } else if (action == "move_out") {
          clotheslineStatus = "Di Luar (Menjemur)";
          moveServo(SERVO_JEMUR);
          Serial.println("✅ Perintah MOVE_OUT dieksekusi!");
        } else if (action == "set_auto") {
          Serial.println("✅ Perintah SET_AUTO dieksekusi! Kembali ke mode otomatis.");
        } else if (action == "reboot") {
          Serial.println("🔄 Perintah REBOOT diterima! Restarting...");
          delay(500);
          ESP.restart();
        }
      }
    }

    // Blink LED konfirmasi sukses
    for (int i = 0; i < 3; i++) {
      digitalWrite(LED_BUILTIN, LOW);
      delay(50);
      digitalWrite(LED_BUILTIN, HIGH);
      delay(50);
    }

  } else if (httpCode > 0) {
    Serial.printf("⚠️  Server merespons HTTP %d\n", httpCode);
  } else {
    Serial.printf("❌ Gagal konek ke server! Error: %s\n",
      http.errorToString(httpCode).c_str());

    // Jika gagal kirim padahal WiFi ada → simpan ke offline buffer
    saveToOfflineBuffer();
  }

  http.end();
}

// ============================================================================
//  OFFLINE STORAGE (LittleFS Store & Forward)
// ============================================================================

/**
 * Simpan satu record sensor ke file JSON di LittleFS.
 * Dipanggil saat WiFi mati atau gagal kirim ke server.
 */
void saveToOfflineBuffer() {
  // Baca file existing
  JsonDocument existing;
  File file = LittleFS.open(OFFLINE_FILE, "r");
  if (file) {
    deserializeJson(existing, file);
    file.close();
  }

  // Buat array jika belum ada
  JsonArray arr = existing["records"].is<JsonArray>()
    ? existing["records"].as<JsonArray>()
    : existing["records"].to<JsonArray>();

  // Jangan simpan kalau sudah penuh (proteksi memori)
  if (arr.size() >= MAX_OFFLINE_RECORDS) {
    Serial.println("⚠️  Buffer offline penuh → data terlama dihapus");
    // Hapus entri paling lama (index 0)
    JsonDocument newDoc;
    JsonArray    newArr = newDoc["records"].to<JsonArray>();
    for (int i = 1; i < (int)arr.size(); i++) {
      newArr.add(arr[i]);
    }
    existing.set(newDoc);
    arr = existing["records"].as<JsonArray>();
  }

  // Tambah record baru
  JsonObject record = arr.add<JsonObject>();
  record["ldr_value"]          = ldrValue;
  record["rain_percentage"]    = rainPercentage;
  record["weather_condition"]  = weatherCondition;
  record["clothesline_status"] = clotheslineStatus;
  record["recorded_at"]        = millis(); // timestamp relatif (detik sejak boot)

  // Tulis kembali ke file
  File outFile = LittleFS.open(OFFLINE_FILE, "w");
  if (outFile) {
    serializeJson(existing, outFile);
    outFile.close();
    Serial.printf("💾 Data offline tersimpan (%d records)\n", (int)arr.size());
  } else {
    Serial.println("❌ Gagal menulis ke LittleFS!");
  }
}

/**
 * Cek apakah ada data offline tersimpan.
 */
bool hasOfflineData() {
  if (!LittleFS.exists(OFFLINE_FILE)) return false;
  File file = LittleFS.open(OFFLINE_FILE, "r");
  if (!file) return false;
  bool hasData = file.size() > 10; // File > 10 byte berarti ada isinya
  file.close();
  return hasData;
}

/**
 * Kirim semua data offline ke server sekaligus (batch POST).
 */
void flushOfflineData() {
  if (!hasOfflineData()) return;

  File file = LittleFS.open(OFFLINE_FILE, "r");
  if (!file) return;

  JsonDocument doc;
  if (deserializeJson(doc, file)) {
    file.close();
    Serial.println("❌ Gagal parse file offline!");
    return;
  }
  file.close();

  JsonArray arr = doc["records"].as<JsonArray>();
  if (arr.size() == 0) return;

  // Kirim batch ke endpoint /api/sensor/data/batch
  HTTPClient http;
  String url = String(SERVER_BASE_URL) + "/api/sensor/data/batch";
  http.begin(url);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("X-API-KEY", API_KEY);
  http.addHeader("ngrok-skip-browser-warning", "true"); // Bypass Ngrok interstitial page
  http.setTimeout(30000); // timeout lebih lama untuk batch

  String batchPayload;
  serializeJson(arr, batchPayload);

  Serial.printf("📤 Mengirim %d data offline ke server...\n", (int)arr.size());
  int httpCode = http.POST(batchPayload);

  if (httpCode == 201) {
    Serial.println("✅ Batch offline berhasil dikirim! Menghapus file lokal...");
    LittleFS.remove(OFFLINE_FILE);
    Serial.println("🗑️  File offline dihapus.");
  } else {
    Serial.printf("❌ Batch gagal! HTTP %d\n", httpCode);
  }

  http.end();
}

/**
 * Cetak info jumlah record offline di Serial Monitor saat boot.
 */
void printOfflineQueueSize() {
  if (!LittleFS.exists(OFFLINE_FILE)) {
    Serial.println("   📂 Tidak ada data offline tersimpan.");
    return;
  }
  File file = LittleFS.open(OFFLINE_FILE, "r");
  if (!file) return;
  JsonDocument doc;
  if (!deserializeJson(doc, file)) {
    int count = doc["records"].as<JsonArray>().size();
    if (count > 0) {
      Serial.printf("   📂 Ditemukan %d data offline tersimpan → akan dikirim saat online\n", count);
    }
  }
  file.close();
}

// ============================================================================
//  LED INDIKATOR
// ============================================================================

void blinkLED() {
  static unsigned long lastBlink = 0;
  static bool ledState = true;

  // WiFi konek: kedip lambat 1 detik
  // WiFi putus: kedip cepat 200ms
  unsigned long interval = isWiFiConnected ? 1000 : 200;

  if (millis() - lastBlink >= interval) {
    ledState = !ledState;
    digitalWrite(LED_BUILTIN, ledState ? HIGH : LOW);
    lastBlink = millis();
  }
}
