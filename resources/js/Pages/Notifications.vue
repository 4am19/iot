<template>
  <div class="max-w-4xl mx-auto pb-12">
     <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10 gap-4">
        <div>
           <h3 class="text-3xl md:text-4xl font-black text-slate-800 tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-rose-600 to-orange-500">Pusat Peringatan</h3>
           <p class="text-slate-500 mt-2 font-medium">Filter cerdas log sistem untuk menampilkan insiden ekstrim dan cuaca buruk.</p>
        </div>
        <button @click="fetchLogs" class="bg-white/80 backdrop-blur-md border border-slate-200 px-6 py-3 rounded-2xl text-sm font-black text-slate-600 hover:bg-slate-50 hover:text-blue-600 shadow-[0_4px_15px_rgba(0,0,0,0.02)] transition-all flex items-center gap-3 group active:scale-95">
           <svg class="w-5 h-5 text-slate-400 group-hover:text-blue-500 transition-colors" :class="isRefreshing ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
           SINKRON ULANG
        </button>
     </div>

     <!-- Loading Skeleton -->
     <div v-if="isLoading" class="space-y-5 animate-pulse">
        <div v-for="n in 3" :key="n" class="bg-white/50 rounded-[2rem] h-32"></div>
     </div>

     <div v-else class="space-y-5 relative z-10">
        <transition-group name="list" appear>
           <div v-for="(alert, index) in filteredAlerts" :key="alert.id" 
                class="p-6 md:p-8 rounded-[2rem] shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-white/50 relative overflow-hidden flex flex-col sm:flex-row items-center sm:items-start gap-6 transition-all hover:-translate-y-1 hover:shadow-xl group backdrop-blur-xl"
                :class="getAlertStyles(alert).bgClass"
                :style="{ animationDelay: `${index * 100}ms` }">
                
                <div class="absolute -right-10 -top-10 w-32 h-32 rounded-full blur-[40px] opacity-40 group-hover:scale-150 transition-transform duration-700" :class="getAlertStyles(alert).glowClass"></div>

                <div class="flex-shrink-0 w-16 h-16 rounded-[1.5rem] flex items-center justify-center text-3xl shadow-lg border" :class="getAlertStyles(alert).iconBg">
                   {{ getAlertStyles(alert).emoji }}
                </div>

                <div class="flex-1 text-center sm:text-left z-10 w-full">
                   <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 mb-2">
                       <h4 class="text-xl md:text-2xl font-black text-slate-800">{{ alert.weather_condition }}</h4>
                       <div class="px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border mx-auto sm:mx-0 shadow-sm" :class="getAlertStyles(alert).badgeClass">
                         {{ alert.clothesline_status === 'Di Dalam' ? '🚨 PROTOKOL EVAKUASI' : '✅ AREA AMAN' }}
                       </div>
                   </div>
                   
                   <p class="text-sm font-medium text-slate-600 mb-3">AI merespon cuaca dan merubah posisi motor menjadi: <strong class="bg-white/50 px-2 py-0.5 rounded shadow-sm text-slate-800">{{ alert.clothesline_status }}</strong></p>
                   
                   <div class="flex items-center justify-center sm:justify-start gap-2 text-xs font-bold uppercase tracking-widest" :class="getAlertStyles(alert).textHint">
                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                     {{ formatDate(alert.created_at) }}
                   </div>
                </div>
           </div>
        </transition-group>

        <div v-if="filteredAlerts.length === 0" class="text-center py-24 bg-white/60 backdrop-blur-md rounded-[3rem] border border-dashed border-slate-300 shadow-sm">
           <div class="inline-flex items-center justify-center w-24 h-24 bg-emerald-50 rounded-full mb-6">
              <span class="text-5xl">🌞</span>
           </div>
           <h3 class="text-slate-800 font-black text-2xl tracking-tight mb-2">Semua Terkendali!</h3>
           <p class="text-slate-500 text-base font-medium max-w-md mx-auto">Tidak ada rekaman cuaca buruk. Sistem beroperasi normal.</p>
        </div>
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
      isLoading: true,
      isRefreshing: false
    }
  },
  computed: {
    filteredAlerts() {
       return this.logs.filter(log => {
          return log.weather_condition.toLowerCase().includes('hujan') || 
                 log.weather_condition.toLowerCase().includes('mendung') ||
                 log.weather_condition.toLowerCase().includes('kembali');
       });
    }
  },
  mounted() {
    this.fetchLogs();
    this.polling = setInterval(() => this.fetchLogs(true), 5000);
  },
  unmounted() {
    clearInterval(this.polling);
  },
  methods: {
    formatDate(dateString) {
      if(!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleString('id-ID', {day: 'numeric', month:'short', hour: '2-digit', minute:'2-digit'});
    },
    async fetchLogs(silent = false) {
      this.isRefreshing = true;
      try {
        const response = await axios.get('/api/logs');
        if(response.data) this.logs = response.data;
        if (!this.isLoading && !silent) {
          this.$emit('toast', { type: 'success', title: 'Data Terbaru', message: `${this.filteredAlerts.length} peringatan ditemukan.` });
        }
      } catch (error) { 
        console.error(error);
        if (!silent) this.$emit('toast', { type: 'error', title: 'Gagal', message: 'Tidak bisa mengambil data log.' });
      }
      finally { this.isLoading = false; this.isRefreshing = false; }
    },
    getAlertStyles(alert) {
       const isDanger = alert.clothesline_status === 'Di Dalam';
       if(isDanger) {
          return {
             bgClass: 'bg-gradient-to-r from-rose-50/90 to-red-50/90 border-rose-100 hover:shadow-rose-100/50',
             iconBg: 'bg-gradient-to-br from-rose-500 to-red-600 text-white shadow-rose-500/40 border-rose-400',
             textHint: 'text-rose-500',
             emoji: '⛈️',
             badgeClass: 'bg-white/80 text-rose-700 border-rose-200',
             glowClass: 'bg-rose-400'
          }
       } else {
          return {
             bgClass: 'bg-gradient-to-r from-emerald-50/90 to-green-50/90 border-emerald-100 hover:shadow-emerald-100/50',
             iconBg: 'bg-gradient-to-br from-emerald-400 to-green-500 text-white shadow-emerald-500/40 border-emerald-400',
             textHint: 'text-emerald-600',
             emoji: '🌤️',
             badgeClass: 'bg-white/80 text-emerald-800 border-emerald-200',
             glowClass: 'bg-emerald-400'
          }
       }
    }
  }
}
</script>

<style scoped>
.list-enter-active,
.list-leave-active {
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}
.list-enter-from {
  opacity: 0;
  transform: translateX(-30px);
}
.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}
</style>
