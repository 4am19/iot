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
          <input v-model="form.mac_address" type="text" required placeholder="Contoh: A1:B2:C3:D4:E5:F6" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:text-white transition-all outline-none">
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
