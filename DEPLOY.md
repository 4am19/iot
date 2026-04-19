# 🚀 Panduan Online Sistem IoT Menggunakan NGROK (Murni Gratis & Tanpa Kartu Kredit)

Dokumen ini menjelaskan cara menjadikan laptop/komputer Anda sendiri sebagai server publik menggunakan alat bernama **Ngrok**. 

Kelebihan metode ini:
1. **100% Gratis**, tanpa perlu verifikasi kartu kredit.
2. Mendapatkan domain tetap *(static domain)* dari Ngrok (contoh: `https://nama-anda.ngrok-free.app`).
3. Sistem IoT (ESP32) dan siapa pun di internet bisa mengakses Dashboard Anda dari HP di mana saja, selama laptop Anda menyala.

---

## FASE 1 — Mendaftar & Klaim Domain Ngrok

1. Buka browser dan pergi ke **[https://ngrok.com/](https://ngrok.com/)**.
2. Klik tombol **Sign Up** di pojok kanan atas, lalu pilih **Sign up with Google** (atau daftar manual menggunakan email).
3. Setelah berhasil login dan masuk ke Dashboard Ngrok:
   - Pada menu di sebelah kiri, klik bagian **Domains**.
   - Di halaman tersebut akan ada satu domain gratis yang diberikan untuk Anda (misalnya: `light-falcon-bold.ngrok-free.app`).
   - Jika belum ada, cari tombol seperti **"Claim Free Static Domain"** atau **"Create Domain"**.
4. **Catat dan salin nama domain tersebut** karena ini yang akan menjadi alamat website (Dashboard IoT) Anda selamanya.

---

## FASE 2 — Install & Hubungkan Ngrok di Laptop

1. Kembali ke Dashboard Ngrok (menu sebelah kiri), klik menu **Setup & Installation**.
2. Download Ngrok sesuai dengan sistem operasi Anda (untuk Windows, klik tombol download file `.zip`).
3. Buka file `.zip` hasil download, di dalamnya ada 1 file bernama `ngrok.exe`. 
4. Extract/keluarkan file `ngrok.exe` tersebut (misalnya letakkan di folder Desktop atau C:).
5. Pada Dashboard Ngrok, cari baris **"Connect your account"** yang memiliki bentuk perintah seperti ini:
   ```cmd
   ngrok config add-authtoken 2X................G7x (← Token rahasia Anda)
   ```
6. Buka terminal (CMD/PowerShell) di laptop Anda, _copy-paste_ perintah authtoken di atas lalu tekan **Enter**. (Langkah otentikasi ini cukup dilakukan 1x seumur hidup di laptop Anda).

---

## FASE 3 — Menyalakan Server (Laravel + Ngrok)

Mulai dari sini adalah rutinitas yang **harus selalu dilakukan** ketika Anda ingin Jemuran Otomatis ini aktif. Anda butuh **2 buah Terminal/CMD** yang harus tetap terbuka.

### Terminal 1: Menyalakan Web Server Laravel
Buka terminal baru, arahkan ke folder project IoT Anda, lalu nyalakan:
```bash
php artisan serve
```
*(Pastikan muncul tulisan: "Server running on [http://127.0.0.1:8000]")*

### Terminal 2: Menyalakan Ngrok
Buka satu terminal lagi, arahkan ke folder tempat Anda meletakkan file `ngrok.exe`, lalu jalankan:
```cmd
ngrok http --domain=NAMA_DOMAIN_ANDA.ngrok-free.app 8000
```
> ⚠️ **Catatan Penting:** Ganti `NAMA_DOMAIN_ANDA.ngrok-free.app` dengan nama domain yang sudah Anda klaim di Fase 1 tadi!

Jika berhasil, di layar akan muncul status **Online** dengan tulisan warna hijau. Biarkan kedua layar hitam (terminal) ini terbuka terus di laptop Anda!

---

## FASE 4 — Menyesuaikan Kode ESP32 (Terakhir!)

Karena alamat "server" Anda sudah berubah ke alamat Cloud/Ngrok, ESP32 harus diberi tahu.

1. Buka file firmware Anda di Arduino IDE: `esp32_firmware/jemuran_otomatis.ino`
2. Cari bagian **KONFIGURASI** (Sitar baris 50-an) dan ganti `SERVER_BASE_URL`:
   ```cpp
   // SEBELUMNYA: 
   // const char* SERVER_BASE_URL = "http://192.168.1.110:8000";

   // GANTI MENJADI DOMAIN NGROK ANDA:
   const char* SERVER_BASE_URL = "https://NAMA_DOMAIN_ANDA.ngrok-free.app";
   ```
3. Jangan lupa sesuaikan juga `API_KEY`! Untuk mengetahuinya:
   - Buka URL Ngrok Anda dari Browser (misal `https://...ngrok-free.app`).
   - Login menggunakan akun `admin@jemuran.com` dengan password `admin123`.
   - Di menu sebelah kiri klik **Settings**. 
   - Salin tulisan acak di kolom **"Device API Key"** ke dalam file `.ino` Anda.
   ```cpp
   const char* API_KEY = "masukkan_api_key_dari_dashboard_di_sini";
   ```
4. Sambungkan ESP32 dengan kabel USB ke laptop.
5. Klik tombol **Upload** di Arduino IDE.

**Selesai! 🎉**

Apabila ESP32 sudah menyala dan terhubung ke WiFi rumah, dia akan otomatis mengirim data cuaca terus-menerus ke Ngrok. Anda bisa dengan bangga memamerkan URL/Situs IoT ini ke teman atau dosen Anda kapan saja, asalkan laptop Anda masih dalam keadaan sedang menjalankan Laravel & Ngrok.
