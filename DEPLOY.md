# 🚀 Panduan Deploy Lengkap — Render.com (Gratis)
## Dari Upload Code sampai ESP32 Terhubung

---

## Gambaran Alur Deploy

```
💻 Laptop Anda                 🌐 GitHub                  ☁️ Render.com
─────────────────              ─────────────────          ─────────────────────────
Project Laravel        →  git push  →  Repository   →   Auto Deploy (build.sh)
ESP32 Firmware (.ino)                                     ↓
                                                     https://jemuran-xxx.onrender.com
                                                          ↓
📱 HP di mana saja  ──────────────── buka URL ────────────↑
📟 ESP32 di rumah   ──── POST /api/sensor/data ───────────↑
```

---

## FASE 1 — Persiapan di Laptop (±10 menit)

### Langkah 1.1 — Build Frontend Terlebih Dahulu

Buka terminal di folder project, jalankan:

```bash
npm run build
```

Tunggu hingga selesai. Ini menghasilkan folder `public/build/` yang berisi file Vue.js siap produksi.

---

### Langkah 1.2 — Inisialisasi Git

Buka terminal di folder `c:\Users\riska\Desktop\IOT\`, jalankan satu per satu:

```bash
git init
git add .
git commit -m "🚀 Initial commit - IoT Jemuran Otomatis v2.0"
```

> ⚠️ Pastikan file `.env` **TIDAK** ikut (sudah ada di `.gitignore`). Jangan pernah commit file `.env`!

---

### Langkah 1.3 — Upload ke GitHub

1. Buka **https://github.com** → Login → Klik tombol **"New repository"** (tombol hijau)
2. Isi form:
   - **Repository name**: `iot-jemuran`
   - **Visibility**: ✅ **Private** (wajib! karena ada kode sistem)
   - **Jangan centang** "Add README" (kita sudah punya file di project)
3. Klik **"Create repository"**
4. GitHub akan tampilkan halaman kosong. Salin perintah bagian **"…or push an existing repository"**:

```bash
git remote add origin https://github.com/USERNAME_ANDA/iot-jemuran.git
git branch -M main
git push -u origin main
```

> Ganti `USERNAME_ANDA` dengan username GitHub Anda yang sebenarnya.

✅ **Selesai jika:** Anda bisa melihat semua file project di halaman GitHub repository.

---

## FASE 2 — Deploy ke Render.com (±15 menit)

### Langkah 2.1 — Daftar & Login Render

1. Buka **https://render.com**
2. Klik **"Get Started for Free"**
3. Pilih **"Continue with GitHub"** → Izinkan akses Render ke GitHub Anda

---

### Langkah 2.2 — Buat Database PostgreSQL Gratis

> Render menggunakan PostgreSQL (bukan MySQL). Kita buat database dulu.

1. Di dashboard Render → Klik **"+ New"** → Pilih **"PostgreSQL"**
2. Isi form:
   - **Name**: `iot-jemuran-db`
   - **Region**: **Singapore (Southeast Asia)** — pilih ini supaya latensi rendah dari Indonesia
   - **Instance Type**: **Free** ✅
3. Klik **"Create Database"**
4. Tunggu ±2 menit sampai status **"Available"**
5. **SALIN** nilai berikut (klik ikon copy di sebelah masing-masing):
   - `Internal Database URL` → untuk diisi ke environment variables nanti

---

### Langkah 2.3 — Buat Web Service (Laravel)

1. Klik **"+ New"** → Pilih **"Web Service"**
2. Pilih **"Build and deploy from a Git repository"** → Klik **"Next"**
3. Hubungkan GitHub: Klik **"Connect GitHub"** → Pilih repository `iot-jemuran` → Klik **"Connect"**
4. Isi konfigurasi:

| Field | Nilai yang diisi |
|---|---|
| **Name** | `iot-jemuran` |
| **Region** | `Singapore (Southeast Asia)` |
| **Branch** | `main` |
| **Runtime** | `PHP` |
| **Build Command** | `chmod +x render-build.sh && ./render-build.sh` |
| **Start Command** | `php artisan serve --host=0.0.0.0 --port=$PORT` |
| **Instance Type** | `Free` ✅ |

5. **Jangan klik Deploy dulu** — lanjut ke Langkah 2.4 dulu!

---

### Langkah 2.4 — Isi Environment Variables

Masih di halaman pembuatan Web Service, scroll ke bawah ke bagian **"Environment Variables"**.

Klik **"Add Environment Variable"** satu per satu dan isi seperti ini:

```
APP_NAME            = Jemuran Otomatis IoT
APP_ENV             = production
APP_DEBUG           = false
APP_URL             = https://iot-jemuran.onrender.com   ← ganti dengan nama project Anda
APP_KEY             = (kosongkan dulu, akan di-generate otomatis)

DB_CONNECTION       = pgsql
DATABASE_URL        = (tempel nilai "Internal Database URL" dari Langkah 2.2)

SESSION_DRIVER      = database
QUEUE_CONNECTION    = database
CACHE_STORE         = database
LOG_LEVEL           = error
```

> **Catatan APP_KEY**: Isi dengan perintah `php artisan key:generate --show` yang Anda jalankan di laptop, lalu tempel hasilnya. Contoh: `base64:ZtcsXxZaGRbH...`

Nilai APP_KEY Anda sudah ada, ambil dari file `.env` lokal di baris `APP_KEY=...`

---

### Langkah 2.5 — Klik Deploy

1. Setelah semua environment variables diisi → Klik **"Create Web Service"**
2. Render akan mulai proses build otomatis:

```
▶ Cloning repository...
▶ Running build command: ./render-build.sh
   🔧 [1/6] Menginstall dependency PHP...
   🔧 [2/6] Build Vue.js frontend...
   🔧 [3/6] Caching konfigurasi...
   🔧 [4/6] Menjalankan migrations...
   🔧 [5/6] Menjalankan seeder (buat akun admin)...
   ✅ Build selesai!
▶ Starting server...
```

3. Tunggu ±5-10 menit sampai status berubah dari 🟡 **"In Progress"** ke 🟢 **"Live"**
4. Setelah Live, Anda akan mendapat URL seperti: `https://iot-jemuran.onrender.com`

✅ **Test:** Buka URL tersebut di browser → Halaman login dashboard IoT harus muncul!

---

### Langkah 2.6 — Login ke Dashboard

1. Buka `https://iot-jemuran.onrender.com` di browser
2. Login dengan akun default:
   ```
   Email    : admin@jemuran.com
   Password : admin123
   ```
3. Masuk ke halaman **Settings** → Lihat dan salin **API Key** (32 karakter hex)

> ⚠️ **SEGERA ganti password** setelah login pertama kali!

---

## FASE 3 — Sambungkan ESP32 ke Cloud (±5 menit)

### Langkah 3.1 — Update Firmware ESP32

Buka file `esp32_firmware/jemuran_otomatis.ino` di Arduino IDE.

Cari baris 50-54 dan ganti dua nilai ini:

```cpp
// SEBELUM (URL lokal):
const char* SERVER_BASE_URL = "http://192.168.1.110:8000";
const char* API_KEY = "GANTI_DENGAN_API_KEY_DARI_DASHBOARD";

// SESUDAH (URL cloud Render):
const char* SERVER_BASE_URL = "https://iot-jemuran.onrender.com";
const char* API_KEY = "salin-api-key-32-karakter-dari-dashboard-settings";
```

---

### Langkah 3.2 — Upload Firmware ke ESP32 (Terakhir Kali via Kabel!)

1. Sambungkan ESP32 ke laptop dengan kabel USB
2. Di Arduino IDE:
   - Pilih **Tools** → **Board**: `ESP32 Dev Module`
   - Pilih **Tools** → **Port**: pilih COM port yang muncul
3. Klik tombol **Upload** (→)
4. Tunggu hingga selesai: `Done uploading`
5. Buka **Serial Monitor** (Ctrl+Shift+M), baud rate **115200**

---

### Langkah 3.3 — Setup WiFi via HP (Hanya Sekali!)

Setelah upload selesai, di Serial Monitor akan muncul:

```
╔══════════════════════════════════════════════════╗
║  🏠 JEMURAN OTOMATIS IoT v2.0                   ║
╚══════════════════════════════════════════════════╝
📶 Menghubungkan ke WiFi...
   📱 WiFi: JemuranSetup
   🔑 Password: 12345678
   🌐 Buka browser → 192.168.4.1 → isi nama WiFi rumah
```

Langkah di HP Anda:
1. Buka **Pengaturan WiFi** di HP
2. Sambungkan ke hotspot **`JemuranSetup`** (password: `12345678`)
3. HP akan otomatis membuka halaman setup (atau buka browser → `192.168.4.1`)
4. Halaman **"Setup Jemuran Otomatis"** akan muncul
5. Pilih nama WiFi rumah Anda → isi password → klik **Save**
6. ESP32 restart otomatis dan tersambung ke WiFi rumah

---

### Langkah 3.4 — Verifikasi Koneksi

Setelah ESP32 restart, di Serial Monitor akan muncul:

```
✅ WiFi Terhubung!
   📍 IP Address: 192.168.1.xxx
   📡 RSSI: -52 dBm

✅ ElegantOTA aktif → Buka http://192.168.1.xxx/update

📤 Kirim → {"ldr_value":75,"rain_percentage":2.1,...}
✅ Server [200]: {"status":"success","data":{...}}
```

Buka dashboard di HP Anda: `https://iot-jemuran.onrender.com`

Jika grafik sensor sudah bergerak dan menampilkan data real-time → **🎉 Berhasil!**

---

## FASE 4 — Uji Coba Kontrol dari Luar Rumah

### Test dari HP di luar jaringan WiFi rumah:

1. Matikan WiFi HP → pakai data seluler (4G)
2. Buka `https://iot-jemuran.onrender.com`
3. Pastikan data sensor masih update
4. Coba tekan **"Tarik Masuk Jemuran"** → tunggu ≤10 detik → servo harus bergerak!

---

## Checklist Final ✅

- [ ] `npm run build` berhasil di laptop
- [ ] Repository GitHub dibuat (Private)
- [ ] Database PostgreSQL dibuat di Render (region Singapore)
- [ ] Web Service deployed, status 🟢 Live
- [ ] Dashboard bisa dibuka di browser
- [ ] Login `admin@jemuran.com / admin123` berhasil
- [ ] API Key disalin dari halaman Settings
- [ ] Firmware ESP32 diupdate dengan URL cloud + API Key baru
- [ ] Firmware diupload ke ESP32 via USB
- [ ] Setup WiFi via HP berhasil (hotspot JemuranSetup)
- [ ] Serial Monitor tampilkan `✅ Server [200]`
- [ ] Dashboard tampilkan data sensor real-time
- [ ] Test kontrol dari HP pakai data seluler (bukan WiFi rumah)
- [ ] Ganti password admin dari `admin123` ke password yang kuat

---

## Troubleshooting Umum

| Masalah | Solusi |
|---|---|
| Build gagal di Render | Cek tab "Logs" di Render, cari baris error merah |
| `DB_CONNECTION error` | Pastikan `DATABASE_URL` sudah diisi dengan benar |
| ESP32 tidak bisa POST | Cek `SERVER_BASE_URL` sudah HTTPS (bukan HTTP) |
| Serial Monitor: `Error: -1` | Server mungkin sedang "tidur" (cold start ~30 detik) |
| Dashboard tidak muncul | Pastikan `APP_URL` di Render sudah pakai URL yang benar |
| API Key invalid (401) | Buka Settings dashboard → salin ulang API Key |

---

## Catatan Penting untuk Render Free Tier

> Render Free Tier akan "tidur" jika tidak ada traffic selama 15 menit.
> Tapi karena **ESP32 mengirim data setiap 10 detik**, server tidak akan pernah tidur
> selama ESP32 menyala di rumah Anda. ✅
