<template>
  <div class="min-h-screen bg-slate-50 dark:bg-[#0a0e1a] flex items-center justify-center p-4 transition-colors duration-500">
    <div class="max-w-md w-full bg-white dark:bg-slate-900 rounded-3xl shadow-xl dark:shadow-indigo-500/10 border border-slate-100 dark:border-white/5 overflow-hidden relative z-10 p-8">
      
      <div class="text-center mb-8">
        <div class="w-16 h-16 mx-auto bg-gradient-to-tr from-indigo-500 to-purple-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-500/30 mb-4 ring-2 ring-white dark:ring-slate-800">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        </div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white">Setup Perangkat</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Hubungkan Jemuran Pintar pertama Anda ke akun ini.</p>
      </div>

      <form @submit.prevent="pairDevice" class="space-y-5">
        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">MAC Address / ID Perangkat</label>
          <div class="relative">
            <input v-model="form.mac_address" type="text" required placeholder="Contoh: A1:B2:C3:D4:E5:F6" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:text-white transition-all outline-none">
            <button type="button" @click="scanBluetooth" class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1.5 bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 rounded-lg text-xs font-bold hover:bg-indigo-200 dark:hover:bg-indigo-500/30 transition-colors flex items-center gap-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              Cari Otomatis
            </button>
          </div>
          <p class="text-[11px] text-slate-400 mt-1.5">Klik Cari Otomatis untuk memindai lewat Bluetooth HP/Laptop.</p>
        </div>
        
        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Nama Perangkat (Opsional)</label>
          <input v-model="form.name" type="text" placeholder="Contoh: Jemuran Balkon" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:text-white transition-all outline-none">
        </div>

        <div v-if="errorMsg" class="p-3 rounded-lg bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-600 dark:text-rose-400 text-sm font-medium">
          {{ errorMsg }}
        </div>

        <button type="submit" :disabled="loading" class="w-full py-3.5 px-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/30 transition-all active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed flex justify-center items-center gap-2">
          <svg v-if="loading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
          {{ loading ? 'Memproses...' : 'Hubungkan Perangkat' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      form: {
        mac_address: '',
        name: ''
      },
      loading: false,
      errorMsg: ''
    }
  },
  methods: {
    async scanBluetooth() {
      if (!navigator.bluetooth) {
        this.errorMsg = 'Browser ini tidak mendukung Web Bluetooth. Gunakan Chrome/Edge di Android atau Desktop.';
        return;
      }
      try {
        this.loading = true;
        this.errorMsg = '';

        // Gunakan namePrefix agar lebih fleksibel + filter Service UUID
        const device = await navigator.bluetooth.requestDevice({
          filters: [
            { namePrefix: 'Jemuran' },
            { services: ['0000ffe0-0000-1000-8000-00805f9b34fb'] }
          ],
          optionalServices: ['0000ffe0-0000-1000-8000-00805f9b34fb']
        });

        this.errorMsg = '';
        
        // Coba baca MAC Address dari characteristic khusus
        try {
          const server = await device.gatt.connect();
          const service = await server.getPrimaryService('0000ffe0-0000-1000-8000-00805f9b34fb');
          const characteristic = await service.getCharacteristic('0000ffe2-0000-1000-8000-00805f9b34fb');
          const value = await characteristic.readValue();
          const mac = new TextDecoder('utf-8').decode(value);
          
          if (mac && mac.includes(':')) {
            this.form.mac_address = mac.trim();
          } else {
            this.form.mac_address = device.id; // fallback ke ID BLE
          }
          
          if (device.gatt.connected) device.gatt.disconnect();
        } catch (gattErr) {
          // Jika gagal baca GATT, gunakan device.id sebagai identifier
          console.warn('Tidak bisa baca characteristic MAC, menggunakan ID BLE:', gattErr);
          this.form.mac_address = device.id;
          this.errorMsg = 'Perangkat ditemukan! Namun MAC Address tidak terbaca otomatis. Silakan lihat Serial Monitor Arduino IDE untuk MAC Address yang benar.';
        }
        
      } catch (error) {
        console.error(error);
        if (error.name === 'NotFoundError') {
          this.errorMsg = '⚠️ Tidak ada perangkat ditemukan. Tips: (1) Pastikan ESP32 sudah dinyalakan. (2) Jika "Jemuran Pintar IoT" muncul di pengaturan Bluetooth Windows/HP, hapus dulu dari sana, lalu coba Cari Otomatis lagi.';
        } else if (error.name === 'SecurityError') {
          this.errorMsg = 'Akses Bluetooth ditolak. Pastikan Bluetooth dan GPS aktif.';
        } else {
          this.errorMsg = 'Gagal scan: ' + error.message;
        }
      } finally {
        this.loading = false;
      }
    },

    async pairDevice() {
      this.loading = true;
      this.errorMsg = '';
      try {
        await axios.post('/api/devices/pair', this.form);
        this.$emit('paired');
      } catch (e) {
        this.errorMsg = e.response?.data?.error || 'Terjadi kesalahan saat menghubungkan perangkat.';
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>
