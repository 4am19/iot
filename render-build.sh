#!/usr/bin/env bash
# =============================================================================
#  render-build.sh — Build Script untuk Render.com
#  File ini dijalankan otomatis oleh Render saat setiap kali deploy.
# =============================================================================

set -o errexit  # Hentikan script jika ada error

echo "🔧 [1/6] Menginstall dependency PHP..."
composer install --no-dev --optimize-autoloader

echo "🔧 [2/6] Menginstall dan build dependency Node.js (Vite/Vue)..."
npm install
npm run build

echo "🔧 [3/6] Menjalankan caching konfigurasi Laravel..."
php artisan config:cache
php artisan route:cache

echo "🔧 [4/6] Menjalankan database migrations..."
php artisan migrate --force

echo "🔧 [5/6] Menjalankan database seeder (akun admin)..."
php artisan db:seed --force

echo "🔧 [6/6] Membersihkan cache lama..."
php artisan view:cache
php artisan event:cache

echo "✅ Build selesai! Aplikasi siap."
