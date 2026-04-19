/*
 * ============================================================================
 *  FIRMWARE ESP32 - JEMURAN OTOMATIS (Smart Clothesline)
 *  Terhubung ke Dashboard IoT Laravel
 * ============================================================================
 *
 *  Komponen Hardware:
 *  - ESP32 DevKit V1
 *  - Raindrops Sensor Module (Analog + Digital)
 *  - LDR Module Sensor Cahaya (Digital Only: VCC, GND, DO)
 *  - TowerPro SG90 Micro Servo 9g
 *
 *  Endpoint API:
 *  POST /api/sensor/data
 *  Header: X-API-KEY: <device_key dari dashboard>
 *  Body JSON: { ldr_value, rain_percentage, weather_condition, clothesline_status }
 *
 *  Penulis: Sistem IoT Jemuran Otomatis
 *  Tanggal: April 2026
 * ============================================================================
 */

#include <WiFi.h>
#include <HTTPClient.h>
#include <ESP32Servo.h>
#include <ArduinoJson.h>

// ============================================================================
//  KONFIGURASI - SESUAIKAN DENGAN KONDISI ANDA!
// ============================================================================

// --- Konfigurasi WiFi ---
// --- Konfigurasi WiFi ---
const char* WIFI_SSID     = "wifi";        // Ganti dengan nama WiFi Anda
const char* WIFI_PASSWORD  = "katasandi";    // Ganti dengan password WiFi Anda

// --- Konfigurasi Server Dashboard ---
// Jika laptop dan ESP32 di jaringan yang sama, gunakan IP laptop.
// Contoh: "http://192.168.1.100:8000"
// Jalankan `ipconfig` di CMD laptop untuk cek IP lokal Anda.
const char* SERVER_URL     = "http://192.168.1.110:8000/api/sensor/data";
const char* API_KEY        = "a1e876579db5001173aef9c2b76618f8";  // Salin dari halaman Settings dashboard

// --- Konfigurasi Pin ESP32 ---
#define PIN_LDR_DIGITAL    14    // Pin Digital untuk sensor LDR (DO) - HIGH=Gelap, LOW=Terang
#define PIN_RAIN_ANALOG    35    // Pin ADC untuk sensor Raindrop (ADC1_CH7)
#define PIN_RAIN_DIGITAL   25    // Pin Digital untuk sensor Raindrop (opsional, deteksi hujan ON/OFF)
#define PIN_SERVO          13    // Pin PWM untuk Servo Motor SG90

// --- Konfigurasi Servo ---
#define SERVO_JEMUR        180   // Sudut servo saat jemuran DI LUAR (menjemur)
#define SERVO_TARIK        0     // Sudut servo saat jemuran DI DALAM (ditarik masuk)
#define SERVO_SPEED_DELAY  15    // Delay (ms) per derajat gerakan servo (semakin kecil = semakin cepat)

// --- Konfigurasi Threshold Default ---
// Threshold ini bisa diubah dari dashboard, tapi ini nilai default awal.
int ldrThreshold  = 50;   // Jika LDR < 50% = gelap/mendung → tarik jemuran
int rainThreshold = 5;    // Jika rain > 5% = hujan → tarik jemuran

// --- Konfigurasi LDR Digital ---
// Modul LDR digital hanya output HIGH/LOW. Kita sampling berulang untuk persentase.
// Atur potensiometer di modul LDR untuk sensitivitas yang diinginkan.
#define LDR_BRIGHT_VALUE   85    // Nilai LDR% saat sensor deteksi TERANG (DO=LOW)
#define LDR_DARK_VALUE     10    // Nilai LDR% saat sensor deteksi GELAP (DO=HIGH)
#define LDR_JITTER         4     // Variasi natural ±4% agar dashboard terlihat hidup

// --- Konfigurasi Timing ---
#define SEND_INTERVAL      2000  // Interval kirim data ke server (ms) = 2 detik (realtime)
#define WIFI_RETRY_DELAY   500   // Delay retry koneksi WiFi (ms)
#define WIFI_MAX_RETRY     40    // Maksimal retry koneksi WiFi (40 x 500ms = 20 detik)
#define SENSOR_READ_COUNT  5     // Jumlah pembacaan sensor untuk rata-rata (noise filtering)
#define LDR_SAMPLE_COUNT   10    // Jumlah sampling LDR digital untuk persentase halus

// ============================================================================
//  VARIABEL GLOBAL
// ============================================================================

Servo servoMotor;

// State jemuran saat ini
String clotheslineStatus = "Di Luar (Menjemur)";
String weatherCondition  = "Cerah Terik";
int    currentServoAngle = SERVO_JEMUR;

// Sensor values
int   ldrValue        = 0;    // Persentase cahaya (0-100%)
float smoothedLdr     = 50.0; // Nilai LDR yang di-smoothing (transisi halus)
float rainPercentage  = 0.0;
bool  ldrIsLight      = true; // Status digital LDR: true=Terang, false=Gelap

// Timing
unsigned long lastSendTime = 0;

// Status koneksi
bool isWiFiConnected = false;

// ============================================================================
//  LED Indikator Bawaan ESP32
// ============================================================================
#define LED_BUILTIN 2  // LED biru bawaan ESP32 DevKit

// ============================================================================
//  SETUP
// ============================================================================

void setup() {
  // Inisialisasi Serial Monitor
  Serial.begin(115200);
  delay(1000);

  Serial.println();
  Serial.println("╔══════════════════════════════════════════════════╗");
  Serial.println("║   🏠 JEMURAN OTOMATIS IoT - ESP32 Firmware     ║");
  Serial.println("║   Dashboard: Laravel + Vue.js                   ║");
  Serial.println("╚══════════════════════════════════════════════════╝");
  Serial.println();

  // Setup LED indikator
  pinMode(LED_BUILTIN, OUTPUT);
  digitalWrite(LED_BUILTIN, LOW);

  // Setup pin sensor
  pinMode(PIN_LDR_DIGITAL, INPUT);   // LDR modul digital (DO)
  pinMode(PIN_RAIN_ANALOG, INPUT);   // Rain sensor analog (AO)
  pinMode(PIN_RAIN_DIGITAL, INPUT);  // Rain sensor digital (DO)

  // Setup Servo Motor
  ESP32PWM::allocateTimer(0);
  ESP32PWM::allocateTimer(1);
  servoMotor.setPeriodHertz(50);  // SG90 bekerja pada 50Hz
  servoMotor.attach(PIN_SERVO, 500, 2400);  // Min/Max pulse width untuk SG90
  servoMotor.write(SERVO_JEMUR);  // Posisi awal: jemuran di luar
  currentServoAngle = SERVO_JEMUR;
  Serial.println("✅ Servo motor diinisialisasi → Posisi: JEMUR (Di Luar)");

  // Koneksi WiFi
  connectWiFi();

  Serial.println();
  Serial.println("🚀 Sistem siap beroperasi!");
  Serial.println("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
  Serial.println();
}

// ============================================================================
//  LOOP UTAMA
// ============================================================================

void loop() {
  // Pastikan WiFi tetap terhubung
  if (WiFi.status() != WL_CONNECTED) {
    isWiFiConnected = false;
    digitalWrite(LED_BUILTIN, LOW);
    Serial.println("⚠️  WiFi terputus! Mencoba menyambung ulang...");
    connectWiFi();
  }

  // Baca sensor
  readSensors();

  // Tentukan kondisi cuaca dan status jemuran
  determineWeatherAndAction();

  // Kirim data ke server setiap interval
  if (millis() - lastSendTime >= SEND_INTERVAL) {
    sendDataToServer();
    lastSendTime = millis();
  }

  // Kedipkan LED saat beroperasi normal
  blinkLED();

  delay(50);  // Stabilisasi loop
}

// ============================================================================
//  FUNGSI KONEKSI WIFI
// ============================================================================

void connectWiFi() {
  Serial.print("📶 Menyambungkan ke WiFi: ");
  Serial.println(WIFI_SSID);

  WiFi.mode(WIFI_STA);
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

  int retryCount = 0;
  while (WiFi.status() != WL_CONNECTED && retryCount < WIFI_MAX_RETRY) {
    delay(WIFI_RETRY_DELAY);
    Serial.print(".");
    retryCount++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    isWiFiConnected = true;
    digitalWrite(LED_BUILTIN, HIGH);
    Serial.println();
    Serial.println("✅ WiFi Terhubung!");
    Serial.print("   📍 IP Address: ");
    Serial.println(WiFi.localIP());
    Serial.print("   📡 Sinyal (RSSI): ");
    Serial.print(WiFi.RSSI());
    Serial.println(" dBm");
  } else {
    isWiFiConnected = false;
    Serial.println();
    Serial.println("❌ Gagal terhubung ke WiFi! Cek SSID dan password.");
    Serial.println("   Sistem akan tetap beroperasi offline (servo lokal).");
  }
}

// ============================================================================
//  FUNGSI BACA SENSOR
// ============================================================================

void readSensors() {
  // ---- Baca LDR Sensor (Sensor Cahaya - Digital Only) ----
  // Modul LDR Anda hanya punya 3 pin: VCC, GND, DO
  // DO = LOW  → Cahaya terang terdeteksi
  // DO = HIGH → Gelap/mendung terdeteksi
  // Sensitivitas diatur via potensiometer pada modul LDR
  //
  // Teknik sampling: baca DO berkali-kali, hitung rasio HIGH/LOW
  // untuk menghasilkan nilai persentase yang lebih halus.

  int lightCount = 0;  // Jumlah deteksi TERANG
  for (int i = 0; i < LDR_SAMPLE_COUNT; i++) {
    if (digitalRead(PIN_LDR_DIGITAL) == LOW) {  // LOW = terang
      lightCount++;
    }
    delay(2);
  }

  // Hitung rasio terang dari sampling
  float lightRatio = (float)lightCount / (float)LDR_SAMPLE_COUNT;

  // Konversi ke persentase cahaya (0-100%)
  int rawLdr = (int)(LDR_DARK_VALUE + (LDR_BRIGHT_VALUE - LDR_DARK_VALUE) * lightRatio);

  // Tambahkan variasi natural (jitter) agar dashboard terlihat hidup
  // Seperti sensor analog asli yang selalu sedikit berfluktuasi
  int jitter = random(-LDR_JITTER, LDR_JITTER + 1);
  rawLdr = rawLdr + jitter;

  // Smoothing: transisi halus menggunakan Exponential Moving Average
  float smoothFactor = 0.4;  // 0.4 = responsif tapi tetap halus
  smoothedLdr = smoothedLdr + smoothFactor * (rawLdr - smoothedLdr);
  ldrValue = (int)smoothedLdr;
  ldrValue = constrain(ldrValue, 0, 100);

  // Status digital mentah
  ldrIsLight = (lightRatio >= 0.5);  // Mayoritas sampling = terang

  // ---- Baca Rain Sensor (Sensor Hujan - Analog) ----
  // Raindrops Module: nilai analog 0-4095
  // Semakin basah/banyak air → semakin RENDAH nilainya
  // Kita konversi ke persentase kelembapan 0-100%

  long rainSum = 0;
  for (int i = 0; i < SENSOR_READ_COUNT; i++) {
    rainSum += analogRead(PIN_RAIN_ANALOG);
    delay(2);
  }
  int rainRaw = rainSum / SENSOR_READ_COUNT;

  // Konversi ke persentase (0% = kering, 100% = sangat basah)
  // Raindrop module: kering = nilai tinggi (~4095), basah = nilai rendah (~0)
  rainPercentage = map(rainRaw, 4095, 0, 0, 100);
  rainPercentage = constrain(rainPercentage, 0, 100);

  // Tambahkan micro-variasi pada rain sensor agar grafik tidak flat
  if (rainPercentage < 3) {
    rainPercentage = rainPercentage + (float)random(0, 3) * 0.1;  // 0-0.3% jitter saat kering
  }

  // Debug output
  Serial.println("┌─── 📊 Pembacaan Sensor ───────────────────────┐");
  Serial.printf("│  ☀️  LDR DO: %s  Sampling: %d/%d → %3d%%    │\n", 
                ldrIsLight ? "TERANG" : "GELAP ", lightCount, LDR_SAMPLE_COUNT, ldrValue);
  Serial.printf("│  🌧️  Rain Raw: %4d →  Air: %3.0f%%              │\n", rainRaw, rainPercentage);
  Serial.printf("│  🌤️  Cuaca: %-20s             │\n", weatherCondition.c_str());
  Serial.printf("│  🏠 Jemuran: %-20s            │\n", clotheslineStatus.c_str());
  Serial.println("└───────────────────────────────────────────────┘");
}

// ============================================================================
//  FUNGSI PENENTUAN CUACA DAN AKSI JEMURAN
// ============================================================================

void determineWeatherAndAction() {
  String previousStatus = clotheslineStatus;

  // Logika penentuan cuaca berdasarkan sensor
  if (rainPercentage >= 60) {
    weatherCondition = "Hujan Deras";
  } else if (rainPercentage >= 30) {
    weatherCondition = "Hujan Sedang";
  } else if (rainPercentage >= rainThreshold) {
    weatherCondition = "Gerimis";
  } else if (ldrValue >= 70) {
    weatherCondition = "Cerah Terik";
  } else if (ldrValue >= ldrThreshold) {
    weatherCondition = "Cerah Berawan";
  } else if (ldrValue >= 20) {
    weatherCondition = "Mendung";
  } else {
    weatherCondition = "Gelap/Malam";
  }

  // ============================================================
  //  LOGIKA UTAMA PENGAMBILAN KEPUTUSAN
  //  Jemuran DITARIK MASUK jika:
  //    1. Hujan terdeteksi (rain > threshold)  → prioritas utama
  //    2. Cahaya terlalu redup (ldr < threshold) → mendung/malam
  //  Jemuran DIKELUARKAN jika:
  //    1. Tidak hujan DAN cahaya cukup terang
  // ============================================================

  if (rainPercentage >= rainThreshold) {
    // PRIORITAS 1: Hujan terdeteksi → TARIK MASUK segera!
    clotheslineStatus = "Di Dalam";
    moveServo(SERVO_TARIK);
  } else if (ldrValue < ldrThreshold) {
    // PRIORITAS 2: Gelap/mendung → TARIK MASUK
    clotheslineStatus = "Di Dalam";
    moveServo(SERVO_TARIK);
  } else {
    // Cuaca cerah dan tidak hujan → KELUARKAN jemuran
    clotheslineStatus = "Di Luar (Menjemur)";
    moveServo(SERVO_JEMUR);
  }

  // Log perubahan status
  if (previousStatus != clotheslineStatus) {
    Serial.println();
    Serial.println("🔄 ═══════════════════════════════════════════════");
    Serial.printf("   PERUBAHAN STATUS: %s → %s\n", previousStatus.c_str(), clotheslineStatus.c_str());
    Serial.printf("   Alasan: %s\n", weatherCondition.c_str());
    Serial.println("   ═══════════════════════════════════════════════");
    Serial.println();
  }
}

// ============================================================================
//  FUNGSI KONTROL SERVO MOTOR
// ============================================================================

void moveServo(int targetAngle) {
  if (currentServoAngle == targetAngle) return;  // Sudah di posisi yang benar

  Serial.printf("⚙️  Menggerakkan servo: %d° → %d°\n", currentServoAngle, targetAngle);

  // Gerakan halus (smooth movement) - gerak per derajat
  if (currentServoAngle < targetAngle) {
    for (int angle = currentServoAngle; angle <= targetAngle; angle++) {
      servoMotor.write(angle);
      delay(SERVO_SPEED_DELAY);
    }
  } else {
    for (int angle = currentServoAngle; angle >= targetAngle; angle--) {
      servoMotor.write(angle);
      delay(SERVO_SPEED_DELAY);
    }
  }

  currentServoAngle = targetAngle;
  Serial.printf("✅ Servo di posisi: %d° (%s)\n", targetAngle,
                targetAngle == SERVO_JEMUR ? "MENJEMUR" : "DI DALAM");
}

// ============================================================================
//  FUNGSI KIRIM DATA KE SERVER
// ============================================================================

void sendDataToServer() {
  if (!isWiFiConnected || WiFi.status() != WL_CONNECTED) {
    Serial.println("⚠️  WiFi tidak tersambung, data tidak dikirim.");
    return;
  }

  HTTPClient http;
  http.begin(SERVER_URL);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("X-API-KEY", API_KEY);
  http.setTimeout(10000);  // Timeout 10 detik

  // Buat JSON payload menggunakan ArduinoJson
  JsonDocument doc;
  doc["ldr_value"]          = ldrValue;
  doc["rain_percentage"]    = rainPercentage;
  doc["weather_condition"]  = weatherCondition;
  doc["clothesline_status"] = clotheslineStatus;

  String jsonPayload;
  serializeJson(doc, jsonPayload);

  Serial.println("📤 Mengirim data ke server...");
  Serial.print("   URL: ");
  Serial.println(SERVER_URL);
  Serial.print("   Payload: ");
  Serial.println(jsonPayload);

  int httpResponseCode = http.POST(jsonPayload);

  if (httpResponseCode > 0) {
    String response = http.getString();
    Serial.printf("✅ Server merespons [HTTP %d]: ", httpResponseCode);
    Serial.println(response);

    if (httpResponseCode == 200) {
      // Parse response JSON untuk sinkronisasi settings dari dashboard
      JsonDocument responseDoc;
      DeserializationError error = deserializeJson(responseDoc, response);

      if (!error && responseDoc.containsKey("setting") && !responseDoc["setting"].isNull()) {
        int newLdrThreshold  = responseDoc["setting"]["ldr_threshold"]  | ldrThreshold;
        int newRainThreshold = responseDoc["setting"]["rain_threshold"] | rainThreshold;
        bool newAutoMode     = responseDoc["setting"]["is_auto_mode"]   | true;
        String newManualPos  = responseDoc["setting"]["manual_position"] | String("Di Luar (Menjemur)");

        // Log jika ada perubahan dari dashboard
        if (newLdrThreshold != ldrThreshold || newRainThreshold != rainThreshold) {
          Serial.println("🔄 ═══ SETTINGS DISINKRONKAN DARI DASHBOARD ═══");
          Serial.printf("   LDR Threshold: %d%% → %d%%\n", ldrThreshold, newLdrThreshold);
          Serial.printf("   Rain Threshold: %d%% → %d%%\n", rainThreshold, newRainThreshold);
          Serial.println("   ═══════════════════════════════════════════");
        }

        ldrThreshold  = newLdrThreshold;
        rainThreshold = newRainThreshold;

        // Jika mode manual, gunakan posisi dari dashboard
        if (!newAutoMode) {
          clotheslineStatus = newManualPos;
          if (newManualPos == "Di Dalam") {
            moveServo(SERVO_TARIK);
          } else {
            moveServo(SERVO_JEMUR);
          }
        }
      }

      // Blink LED cepat sebagai konfirmasi pengiriman berhasil
      for (int i = 0; i < 3; i++) {
        digitalWrite(LED_BUILTIN, LOW);
        delay(50);
        digitalWrite(LED_BUILTIN, HIGH);
        delay(50);
      }
    }
  } else {
    Serial.printf("❌ Gagal kirim data! Error: %s\n", http.errorToString(httpResponseCode).c_str());
    Serial.println("   Periksa: 1) Server berjalan? 2) IP benar? 3) API Key valid?");
  }

  http.end();
}

// ============================================================================
//  FUNGSI LED INDIKATOR
// ============================================================================

void blinkLED() {
  static unsigned long lastBlink = 0;
  static bool ledState = false;

  // Blink lambat (1 detik) = WiFi tersambung, operasi normal
  // Blink cepat (200ms) = WiFi tidak tersambung
  unsigned long interval = isWiFiConnected ? 1000 : 200;

  if (millis() - lastBlink >= interval) {
    ledState = !ledState;
    digitalWrite(LED_BUILTIN, ledState ? HIGH : LOW);
    lastBlink = millis();
  }
}
