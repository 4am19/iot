<template>
  <div class="bg-white/80 dark:bg-white/[0.04] backdrop-blur-2xl rounded-[2.5rem] p-8 md:p-10 shadow-[0_8px_30px_rgba(0,0,0,0.03)] dark:shadow-none border border-white/50 dark:border-white/[0.08] h-[calc(100vh-10rem)] flex flex-col relative overflow-hidden transition-colors duration-500">
     <div class="absolute -right-20 -top-20 w-64 h-64 bg-slate-100 dark:bg-indigo-500/10 rounded-full blur-[80px] pointer-events-none transition-colors duration-500"></div>
     <div class="absolute -left-20 bottom-0 w-80 h-80 bg-blue-50 dark:bg-purple-500/10 rounded-full blur-[100px] pointer-events-none transition-colors duration-500"></div>

     <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 relative z-10 gap-4">
        <div>
           <h3 class="text-3xl md:text-4xl font-black tracking-tight text-slate-800 dark:text-slate-100 bg-clip-text text-transparent bg-gradient-to-r from-slate-800 to-slate-500 dark:from-slate-100 dark:to-slate-400 transition-colors">Log Resolusi AI</h3>
           <p class="text-slate-500 dark:text-slate-400 text-base mt-2 font-medium transition-colors">Rekam jejak setiap keputusan algoritma dan data sensori mentah.</p>
        </div>
        <div class="flex items-center gap-3">
           <!-- Export Button -->
           <button @click="exportCSV" class="bg-white dark:bg-white/[0.05] flex items-center px-5 py-3 rounded-2xl border border-slate-100 dark:border-white/10 shadow-sm dark:shadow-none hover:-translate-y-1 hover:shadow-md dark:hover:bg-white/10 transition-all gap-2 text-sm font-bold text-slate-600 dark:text-slate-300 active:scale-95">
              <svg class="w-4 h-4 text-slate-400 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
              Export CSV
           </button>
           <div class="bg-white dark:bg-white/[0.05] flex items-center px-6 py-3 rounded-2xl border border-slate-100 dark:border-white/10 shadow-sm dark:shadow-none hover:-translate-y-1 transition-transform">
              <div class="w-12 h-12 bg-blue-50 dark:bg-indigo-500/15 text-blue-600 dark:text-indigo-400 rounded-xl flex items-center justify-center font-black text-2xl mr-4 border border-blue-100 dark:border-indigo-500/30 tabular-nums transition-colors">
                 {{ logs.length }}
              </div>
              <div class="flex flex-col">
                 <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest transition-colors">Database</span>
                 <span class="text-lg font-bold text-slate-700 dark:text-slate-200 leading-tight transition-colors">Total Entri</span>
              </div>
           </div>
        </div>
     </div>

     <!-- Loading Skeleton -->
     <div v-if="isLoading" class="flex-1 rounded-[1.5rem] bg-white dark:bg-white/[0.02] border border-slate-100 dark:border-white/5 animate-pulse transition-colors"></div>

     <!-- Modern Data Grid Container -->
     <div v-else class="flex-1 overflow-auto rounded-[1.5rem] bg-white dark:bg-white/[0.02] border border-slate-100 dark:border-white/5 shadow-inner dark:shadow-none relative z-10 custom-scrollbar transition-colors">
        <table class="min-w-full divide-y divide-slate-100 dark:divide-white/5">
          <thead class="bg-slate-50/90 dark:bg-slate-800/100 backdrop-blur-lg sticky top-0 z-20 transition-colors border-b border-slate-200 dark:border-white/10">
            <tr>
              <th scope="col" class="px-6 md:px-8 py-5 text-left text-[11px] font-black text-slate-500 dark:text-slate-300 uppercase tracking-widest">Timestamp</th>
              <th scope="col" class="px-6 md:px-8 py-5 text-left text-[11px] font-black text-slate-500 dark:text-slate-300 uppercase tracking-widest">Ambien</th>
              <th scope="col" class="px-6 md:px-8 py-5 text-left text-[11px] font-black text-slate-500 dark:text-slate-300 uppercase tracking-widest">LDR</th>
              <th scope="col" class="px-6 md:px-8 py-5 text-left text-[11px] font-black text-slate-500 dark:text-slate-300 uppercase tracking-widest">Hujan</th>
              <th scope="col" class="px-6 md:px-8 py-5 text-left text-[11px] font-black text-slate-500 dark:text-slate-300 uppercase tracking-widest">Motor</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50 dark:divide-white/5 bg-transparent relative transition-colors">
            <tr v-for="(log, index) in logs" :key="log.id" class="group transition-all duration-300 hover:bg-slate-50/80 dark:hover:bg-white/[0.02] cursor-default relative"
                :style="{ animationDelay: `${index * 30}ms` }">
              <td class="px-6 md:px-8 py-5 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                     <div class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-600 group-hover:bg-blue-400 dark:group-hover:bg-indigo-400 transition-colors"></div>
                     <span class="text-sm font-bold text-slate-700 dark:text-slate-300 transition-colors">{{ formatDate(log.created_at) }}</span>
                  </div>
              </td>
              <td class="px-6 md:px-8 py-5 whitespace-nowrap">
                <span class="px-3.5 py-1.5 inline-flex text-xs font-black uppercase tracking-wider rounded-full shadow-sm dark:shadow-none transition-colors"
                      :class="log.weather_condition.includes('Cerah') ? 'bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20' : log.weather_condition.includes('Hujan') ? 'bg-rose-100 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20' : 'bg-slate-100 dark:bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-500/20'">
                  <span class="mr-1.5 text-sm">{{ log.weather_condition.includes('Cerah') ? '☀️' : log.weather_condition.includes('Hujan') ? '🌧️' : '☁️' }}</span> {{ log.weather_condition }}
                </span>
              </td>
              <td class="px-6 md:px-8 py-5 whitespace-nowrap">
                 <div class="flex items-center gap-4 w-full max-w-[150px]">
                    <span class="text-sm font-black text-slate-700 dark:text-slate-300 w-12 tabular-nums transition-colors">{{ log.ldr_value }}%</span>
                    <div class="flex-1 bg-slate-100 dark:bg-white/5 rounded-full h-2 overflow-hidden shadow-inner dark:shadow-none flex transition-colors">
                       <div class="bg-gradient-to-r from-amber-300 to-orange-400 h-full rounded-full transition-all group-hover:shadow-[0_0_10px_rgba(251,146,60,0.6)]" :style="`width: ${log.ldr_value}%`"></div>
                    </div>
                 </div>
              </td>
              <td class="px-6 md:px-8 py-5 whitespace-nowrap">
                 <div class="flex items-center gap-4 w-full max-w-[150px]">
                    <span class="text-sm font-black text-slate-700 dark:text-slate-300 w-12 tabular-nums transition-colors">{{ log.rain_percentage }}%</span>
                    <div class="flex-1 bg-slate-100 dark:bg-white/5 rounded-full h-2 overflow-hidden shadow-inner dark:shadow-none flex transition-colors">
                       <div class="bg-gradient-to-r from-blue-300 to-indigo-500 h-full rounded-full transition-all group-hover:shadow-[0_0_10px_rgba(99,102,241,0.6)]" :style="`width: ${log.rain_percentage}%`"></div>
                    </div>
                 </div>
              </td>
              <td class="px-6 md:px-8 py-5 whitespace-nowrap">
                 <div class="flex items-center gap-3">
                    <div class="p-2 rounded-xl transition-colors" :class="log.clothesline_status === 'Di Dalam' ? 'bg-slate-100 dark:bg-white/5 text-slate-500 dark:text-slate-400' : 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 shadow-[0_0_15px_rgba(16,185,129,0.3)] dark:shadow-[0_0_15px_rgba(16,185,129,0.15)]'">
                       <svg v-if="log.clothesline_status === 'Di Dalam'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                       <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </div>
                    <span class="font-extrabold text-sm transition-colors" :class="log.clothesline_status === 'Di Dalam' ? 'text-slate-500 dark:text-slate-400' : 'text-emerald-600 dark:text-emerald-400'">
                       {{ log.clothesline_status }}
                    </span>
                 </div>
              </td>
            </tr>
            <tr v-if="logs.length === 0">
               <td colspan="5" class="px-8 py-20 text-center">
                  <div class="flex flex-col items-center justify-center opacity-50">
                     <svg class="w-16 h-16 text-slate-300 dark:text-slate-600 mb-4 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                     <p class="text-slate-500 dark:text-slate-400 font-bold text-lg transition-colors">Log Historis Kosong</p>
                     <p class="text-slate-400 dark:text-slate-500 text-sm mt-1 transition-colors">Belum ada pergerakan atau data dari ESP32.</p>
                  </div>
               </td>
            </tr>
          </tbody>
        </table>
     </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  emits: ['toast'],
  data() {
    return {
      logs: [],
      isLoading: true
    }
  },
  mounted() {
    this.fetchLogs();
    this.polling = setInterval(this.fetchLogs, 5000);
  },
  unmounted() {
    clearInterval(this.polling);
  },
  methods: {
    formatDate(dateString) {
      if(!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleString('id-ID', {day: 'numeric', month:'short', hour: '2-digit', minute:'2-digit', second:'2-digit'});
    },
    async fetchLogs() {
      try {
        const response = await axios.get('/api/logs');
        if(response.data) {
          this.logs = response.data;
        }
      } catch (error) { console.error("Gagal get logs", error); }
      finally { this.isLoading = false; }
    },
    exportCSV() {
      if (!this.logs.length) {
        this.$emit('toast', { type: 'error', title: 'Ekspor Gagal', message: 'Tidak ada data untuk diekspor.' });
        return;
      }
      const headers = ['Waktu', 'Cuaca', 'LDR (%)', 'Hujan (%)', 'Posisi Motor'];
      const rows = this.logs.map(l => [
        this.formatDate(l.created_at),
        l.weather_condition,
        l.ldr_value,
        l.rain_percentage,
        l.clothesline_status
      ]);
      const csv = [headers.join(','), ...rows.map(r => r.join(','))].join('\n');
      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
      const link = document.createElement('a');
      link.href = URL.createObjectURL(blob);
      link.download = `iot_jemuran_log_${new Date().toISOString().split('T')[0]}.csv`;
      link.click();
      this.$emit('toast', { type: 'success', title: 'Berhasil!', message: 'File CSV berhasil diunduh.' });
    }
  }
}
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(241, 245, 249, 0.5);
  border-radius: 10px;
}
:global(.dark) .custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.05);
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(203, 213, 225, 0.8);
  border-radius: 10px;
}
:global(.dark) .custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.15);
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(148, 163, 184, 1);
}
:global(.dark) .custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.25);
}
</style>
