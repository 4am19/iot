<template>
  <div class="bg-white/80 backdrop-blur-2xl rounded-[2.5rem] p-8 md:p-10 shadow-[0_8px_30px_rgba(0,0,0,0.03)] border border-white h-[calc(100vh-10rem)] flex flex-col relative overflow-hidden">
     <div class="absolute -right-20 -top-20 w-64 h-64 bg-slate-100 rounded-full blur-[80px] pointer-events-none"></div>
     <div class="absolute -left-20 bottom-0 w-80 h-80 bg-blue-50 rounded-full blur-[100px] pointer-events-none"></div>

     <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 relative z-10 gap-4">
        <div>
           <h3 class="text-3xl md:text-4xl font-black tracking-tight text-slate-800 bg-clip-text text-transparent bg-gradient-to-r from-slate-800 to-slate-500">Log Resolusi AI</h3>
           <p class="text-slate-500 text-base mt-2 font-medium">Rekam jejak setiap keputusan algoritma dan data sensori mentah.</p>
        </div>
        <div class="flex items-center gap-3">
           <!-- Export Button -->
           <button @click="exportCSV" class="bg-white flex items-center px-5 py-3 rounded-2xl border border-slate-100 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all gap-2 text-sm font-bold text-slate-600 active:scale-95">
              <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
              Export CSV
           </button>
           <div class="bg-white flex items-center px-6 py-3 rounded-2xl border border-slate-100 shadow-sm hover:-translate-y-1 transition-transform">
              <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center font-black text-2xl mr-4 border border-blue-100 tabular-nums">
                 {{ logs.length }}
              </div>
              <div class="flex flex-col">
                 <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Database</span>
                 <span class="text-lg font-bold text-slate-700 leading-tight">Total Entri</span>
              </div>
           </div>
        </div>
     </div>

     <!-- Loading Skeleton -->
     <div v-if="isLoading" class="flex-1 rounded-[1.5rem] bg-white border border-slate-100 animate-pulse"></div>

     <!-- Modern Data Grid Container -->
     <div v-else class="flex-1 overflow-auto rounded-[1.5rem] bg-white border border-slate-100 shadow-inner relative z-10 custom-scrollbar">
        <table class="min-w-full divide-y divide-slate-100">
          <thead class="bg-slate-50/90 backdrop-blur-lg sticky top-0 z-20">
            <tr>
              <th scope="col" class="px-6 md:px-8 py-5 text-left text-xs font-black text-slate-400 uppercase tracking-wider">Timestamp</th>
              <th scope="col" class="px-6 md:px-8 py-5 text-left text-xs font-black text-slate-400 uppercase tracking-wider">Ambien</th>
              <th scope="col" class="px-6 md:px-8 py-5 text-left text-xs font-black text-slate-400 uppercase tracking-wider">LDR</th>
              <th scope="col" class="px-6 md:px-8 py-5 text-left text-xs font-black text-slate-400 uppercase tracking-wider">Hujan</th>
              <th scope="col" class="px-6 md:px-8 py-5 text-left text-xs font-black text-slate-400 uppercase tracking-wider">Motor</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50 bg-transparent relative">
            <tr v-for="(log, index) in logs" :key="log.id" class="group transition-all duration-300 hover:bg-slate-50/80 cursor-default relative"
                :style="{ animationDelay: `${index * 30}ms` }">
              <td class="px-6 md:px-8 py-5 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                     <div class="w-2 h-2 rounded-full bg-slate-300 group-hover:bg-blue-400 transition-colors"></div>
                     <span class="text-sm font-bold text-slate-700">{{ formatDate(log.created_at) }}</span>
                  </div>
              </td>
              <td class="px-6 md:px-8 py-5 whitespace-nowrap">
                <span class="px-3.5 py-1.5 inline-flex text-xs font-black uppercase tracking-wider rounded-full shadow-sm"
                      :class="log.weather_condition.includes('Cerah') ? 'bg-amber-100 text-amber-700 border border-amber-200' : log.weather_condition.includes('Hujan') ? 'bg-rose-100 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-600 border border-slate-200'">
                  <span class="mr-1.5 text-sm">{{ log.weather_condition.includes('Cerah') ? '☀️' : log.weather_condition.includes('Hujan') ? '🌧️' : '☁️' }}</span> {{ log.weather_condition }}
                </span>
              </td>
              <td class="px-6 md:px-8 py-5 whitespace-nowrap">
                 <div class="flex items-center gap-4 w-full max-w-[150px]">
                    <span class="text-sm font-black text-slate-700 w-12 tabular-nums">{{ log.ldr_value }}%</span>
                    <div class="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden shadow-inner flex">
                       <div class="bg-gradient-to-r from-amber-300 to-orange-400 h-full rounded-full transition-all group-hover:shadow-[0_0_10px_rgba(251,146,60,0.6)]" :style="`width: ${log.ldr_value}%`"></div>
                    </div>
                 </div>
              </td>
              <td class="px-6 md:px-8 py-5 whitespace-nowrap">
                 <div class="flex items-center gap-4 w-full max-w-[150px]">
                    <span class="text-sm font-black text-slate-700 w-12 tabular-nums">{{ log.rain_percentage }}%</span>
                    <div class="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden shadow-inner flex">
                       <div class="bg-gradient-to-r from-blue-300 to-indigo-500 h-full rounded-full transition-all group-hover:shadow-[0_0_10px_rgba(99,102,241,0.6)]" :style="`width: ${log.rain_percentage}%`"></div>
                    </div>
                 </div>
              </td>
              <td class="px-6 md:px-8 py-5 whitespace-nowrap">
                 <div class="flex items-center gap-3">
                    <div class="p-2 rounded-xl" :class="log.clothesline_status === 'Di Dalam' ? 'bg-slate-100 text-slate-500' : 'bg-emerald-100 text-emerald-600 shadow-[0_0_15px_rgba(16,185,129,0.3)]'">
                       <svg v-if="log.clothesline_status === 'Di Dalam'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                       <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </div>
                    <span class="font-extrabold text-sm" :class="log.clothesline_status === 'Di Dalam' ? 'text-slate-500' : 'text-emerald-600'">
                       {{ log.clothesline_status }}
                    </span>
                 </div>
              </td>
            </tr>
            <tr v-if="logs.length === 0">
               <td colspan="5" class="px-8 py-20 text-center">
                  <div class="flex flex-col items-center justify-center opacity-50">
                     <svg class="w-16 h-16 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                     <p class="text-slate-500 font-bold text-lg">Log Historis Kosong</p>
                     <p class="text-slate-400 text-sm mt-1">Belum ada pergerakan atau data dari ESP32.</p>
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
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(203, 213, 225, 0.8);
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(148, 163, 184, 1);
}
</style>
