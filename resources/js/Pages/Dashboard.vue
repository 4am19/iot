<template>
  <div class="space-y-8">
        
        <!-- Loading Skeleton -->
        <div v-if="isLoading" class="space-y-8 animate-pulse">
           <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
              <div class="xl:col-span-2 bg-white/50 rounded-[2rem] h-72"></div>
              <div class="bg-white/50 rounded-[2rem] h-72"></div>
           </div>
           <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
              <div class="bg-white/50 rounded-2xl h-32" v-for="n in 4" :key="n"></div>
           </div>
           <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
              <div class="bg-white/50 rounded-[2rem] h-80"></div>
              <div class="bg-white/50 rounded-[2rem] h-80"></div>
           </div>
        </div>

        <!-- Actual Content -->
        <template v-else>
        <!-- Top Row -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
          
          <!-- Hero Banner -->
          <div class="xl:col-span-2 bg-gradient-to-br from-[#1A365D] via-[#2B6CB0] to-[#3182CE] rounded-[2rem] p-8 md:p-10 text-white shadow-2xl relative overflow-hidden flex flex-col justify-center border border-white/10 group">
             <div class="absolute top-0 right-0 w-96 h-96 bg-yellow-300 opacity-20 rounded-full -mr-32 -mt-32 blur-[80px] pointer-events-none transition-transform duration-1000 group-hover:scale-110"></div>
             <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-300 opacity-10 rounded-full -ml-20 -mb-20 blur-[60px] pointer-events-none"></div>

             <div class="flex flex-col md:flex-row items-center gap-8 md:gap-12 z-10 w-full justify-between relative">
                <div class="w-full text-center md:text-left">
                  <div class="bg-white/10 backdrop-blur-md px-5 py-2 rounded-full inline-block text-white text-xs font-bold tracking-widest mb-6 border border-white/20 shadow-lg shadow-black/10 transition-transform hover:-translate-y-0.5">
                    STATUS AKTUAL
                  </div>
                  <h3 class="text-5xl md:text-6xl font-black mb-3 tracking-tighter drop-shadow-md bg-clip-text text-transparent bg-gradient-to-r from-white to-blue-100">
                    {{ settings.is_auto_mode ? latestData.clothesline_status : settings.manual_position || 'Memuat...' }}
                  </h3>
                  
                  <div class="flex items-center justify-center md:justify-start gap-4 mb-8">
                     <span class="relative flex h-3 w-3">
                       <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" :class="settings.is_auto_mode ? 'bg-emerald-400' : 'bg-rose-400'"></span>
                       <span class="relative inline-flex rounded-full h-3 w-3" :class="settings.is_auto_mode ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                     </span>
                     <p class="text-blue-100/90 text-sm md:text-base font-semibold tracking-wide">
                       {{ settings.is_auto_mode ? 'Kecerdasan Buatan (Auto)' : 'Tertahan Mode Manual' }}
                     </p>
                  </div>
                  
                  <div class="flex gap-6 justify-center md:justify-start bg-white/5 backdrop-blur-sm p-4 rounded-2xl border border-white/10 w-max mx-auto md:mx-0 shadow-inner">
                     <div>
                        <p class="text-blue-200/70 text-[10px] font-bold uppercase tracking-[0.2em] mb-1">Cuaca</p>
                        <p class="text-2xl font-extrabold tracking-tight text-white drop-shadow-sm">{{ latestData.weather_condition || 'Memuat...' }}</p>
                     </div>
                  </div>
                </div>

                <div class="flex-shrink-0 relative mt-8 md:mt-0">
                   <div class="absolute inset-0 rounded-full blur-[40px] animate-pulse" :class="clotheslineStatus === 'Di Dalam' ? 'bg-slate-400/50' : 'bg-yellow-400/60'"></div>
                   
                   <div :class="clotheslineStatus === 'Di Dalam' ? 'bg-gradient-to-b from-slate-100 to-slate-300 text-slate-700 shadow-[0_10px_30px_rgba(148,163,184,0.6)] border-slate-50' : 'bg-gradient-to-tr from-yellow-300 via-yellow-400 to-orange-400 text-white shadow-[0_10px_40px_rgba(250,204,21,0.6)] border-yellow-200'" 
                        class="relative w-40 h-40 md:w-44 md:h-44 rounded-full transition-all duration-700 flex items-center justify-center text-7xl md:text-8xl transform hover:scale-110 hover:rotate-6 border-4 z-10 group-hover:shadow-2xl">
                      {{ clotheslineStatus === 'Di Dalam' ? '☁️' : '☀️' }}
                   </div>
                </div>
             </div>
             
             <transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 translate-y-4" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-200" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-4">
               <div v-if="!settings.is_auto_mode" class="absolute bottom-6 right-8 bg-rose-500/90 backdrop-blur-md px-5 py-2.5 rounded-2xl text-xs font-black text-white shadow-xl shadow-rose-500/20 border border-rose-400 flex items-center gap-2">
                  <span class="text-xl animate-bounce">⚠️</span> MANUAL OVERRIDE AKTIF
               </div>
             </transition>
          </div>

          <!-- Gauges (Sensor Realtime) -->
          <div class="bg-white/70 backdrop-blur-xl rounded-[2rem] p-8 shadow-[0_8px_30px_rgba(0,0,0,0.04)] border border-white flex flex-col justify-center relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-b from-white/50 to-transparent pointer-events-none"></div>
            <h3 class="text-slate-400 font-extrabold text-[11px] uppercase tracking-[0.25em] mb-8 text-center relative z-10">Data Sensor Fisik Realtime</h3>
            
            <div class="flex flex-col gap-8 items-center w-full relative z-10">
               <!-- LDR Gauge -->
               <div class="w-full relative">
                 <div class="flex justify-between items-end mb-3 px-1">
                    <span class="font-bold text-slate-700 flex items-center gap-2"><span class="text-2xl drop-shadow-sm">☀️</span> LDR Matahari</span>
                    <span class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-500 tracking-tighter tabular-nums">{{ displayLdr }}<span class="text-lg font-bold">%</span></span>
                 </div>
                 <div class="w-full bg-slate-100 h-6 rounded-full overflow-hidden shadow-inner border border-slate-200/50 p-1">
                    <div class="h-full rounded-full bg-gradient-to-r from-yellow-300 via-amber-400 to-orange-500 relative transition-all duration-1000 ease-out shadow-[0_0_15px_rgba(251,191,36,0.5)]" :style="{ width: (latestData.ldr_value || 0) + '%' }">
                       <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCI+PHBhdGggZD0iTTAgMjBMMjAgMEwxMCAwTDAgMTB6TTAgMEwyMCAyMEwyMCAxMEwwIDAiIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4yIi8+PC9zdmc+')] bg-[length:20px_20px] animate-[slide_1s_linear_infinite]"></div>
                    </div>
                 </div>
               </div>
               
               <!-- Rain Gauge -->
               <div class="w-full relative">
                 <div class="flex justify-between items-end mb-3 px-1">
                    <span class="font-bold text-slate-700 flex items-center gap-2"><span class="text-2xl drop-shadow-sm">🌧️</span> Volume Air</span>
                    <span class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 to-blue-600 tracking-tighter tabular-nums">{{ displayRain }}<span class="text-lg font-bold">%</span></span>
                 </div>
                 <div class="w-full bg-slate-100 h-6 rounded-full overflow-hidden shadow-inner border border-slate-200/50 p-1">
                    <div class="h-full rounded-full bg-gradient-to-r from-blue-400 via-indigo-500 to-violet-600 relative transition-all duration-1000 ease-out shadow-[0_0_15px_rgba(99,102,241,0.5)]" :style="{ width: (latestData.rain_percentage || 0) + '%' }">
                       <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCI+PHBhdGggZD0iTTAgMjBMMjAgMEwxMCAwTDAgMTB6TTAgMEwyMCAyMEwyMCAxMEwwIDAiIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4yIi8+PC9zdmc+')] bg-[length:20px_20px] animate-[slide_1s_linear_infinite]"></div>
                    </div>
                 </div>
               </div>
            </div>
          </div>
        </div>

        <!-- Summary Stats Row -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
           <div v-for="stat in summaryStats" :key="stat.label" class="bg-white/70 backdrop-blur-xl rounded-2xl p-5 md:p-6 border border-white shadow-[0_4px_20px_rgba(0,0,0,0.03)] group hover:-translate-y-1 transition-all duration-300 cursor-default">
              <div class="flex items-center gap-3 mb-3">
                 <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl shadow-sm" :class="stat.bgClass">{{ stat.emoji }}</div>
                 <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 leading-tight">{{ stat.label }}</span>
              </div>
              <p class="text-3xl md:text-4xl font-black tracking-tighter tabular-nums" :class="stat.valueClass">{{ stat.value }}<span class="text-base font-bold ml-0.5">{{ stat.unit }}</span></p>
           </div>
        </div>

        <!-- Chart + Control Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
           
           <!-- Real-time Line Chart -->
           <div class="bg-white/70 backdrop-blur-xl rounded-[2rem] p-6 md:p-8 shadow-[0_8px_30px_rgba(0,0,0,0.04)] border border-white relative overflow-hidden">
               <div class="flex justify-between items-center mb-6">
                  <h3 class="text-slate-400 font-extrabold text-[11px] uppercase tracking-[0.25em]">Tren Sensor Real-time</h3>
                  <span class="text-[10px] bg-emerald-50 text-emerald-600 px-3 py-1 rounded-full font-bold border border-emerald-100">LIVE</span>
               </div>
               <div class="relative w-full" style="height: 260px;">
                  <canvas ref="sensorChart"></canvas>
               </div>
           </div>

           <!-- Main Switch -->
           <div class="bg-white/70 backdrop-blur-xl rounded-[2rem] p-8 shadow-[0_8px_30px_rgba(0,0,0,0.04)] border border-white flex flex-col items-center justify-center min-h-[300px] relative overflow-hidden">
               <div class="absolute inset-0 bg-blue-50/30"></div>
               <h3 class="text-slate-400 font-extrabold text-[11px] uppercase tracking-[0.25em] mb-4 relative z-10">Akses AI Induk</h3>
               <p class="text-center text-sm text-slate-500 mb-10 max-w-sm font-medium relative z-10">Matikan saklar ini jika Anda ingin mengambil alih pergerakan rel motor secara penuh.</p>
               
               <div class="flex flex-col items-center justify-center relative z-10">
                 <button @click="toggleAutoMode" 
                         class="relative inline-flex h-20 w-40 items-center rounded-full transition-all duration-500 focus:outline-none focus:ring-4 focus:ring-offset-4 focus:ring-blue-100 cursor-pointer border-[6px]"
                         :class="settings.is_auto_mode ? 'bg-emerald-500 border-emerald-100 shadow-[0_0_40px_rgba(16,185,129,0.4)] hover:bg-emerald-400' : 'bg-slate-300 border-slate-100 shadow-inner hover:bg-slate-200'">
                   <span class="inline-block h-16 w-16 transform rounded-full bg-white shadow-[0_4px_12px_rgba(0,0,0,0.15)] transition-transform duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)] flex items-center justify-center"
                         :class="settings.is_auto_mode ? 'translate-x-20' : 'translate-x-1'">
                      <svg v-if="settings.is_auto_mode" class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                      <svg v-else class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                   </span>
                 </button>
                 <span class="font-black text-2xl tracking-tight mt-6" :class="settings.is_auto_mode ? 'text-emerald-600' : 'text-slate-600'">
                    {{ settings.is_auto_mode ? '🤖 KENDALI AUTO' : '✋ OVERRIDE MANUAL'}}
                 </span>
               </div>
           </div>
        </div>

        <!-- Manual Buttons Section -->
        <div class="bg-white/70 backdrop-blur-xl rounded-[2rem] p-8 shadow-[0_8px_30px_rgba(0,0,0,0.04)] border border-white relative">
            <h3 class="text-slate-400 font-extrabold text-[11px] uppercase tracking-[0.25em] mb-6 text-center">Eksekusi Rel Motor Manual</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
               <button @click="setManualPosition('Di Luar (Menjemur)')" :disabled="settings.is_auto_mode" 
                       class="w-full py-6 bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-700 hover:from-blue-600 hover:to-indigo-800 text-white disabled:from-slate-100 disabled:to-slate-200 disabled:text-slate-400 font-black text-xl rounded-[1.5rem] shadow-xl hover:shadow-blue-500/40 disabled:shadow-inner transition-all duration-300 focus:ring-4 ring-blue-200 outline-none transform active:scale-[0.98] disabled:active:scale-100 flex justify-center items-center gap-4 group">
                  <div class="p-2 bg-white/20 rounded-xl group-hover:scale-110 transition-transform">
                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                  </div>
                  <span>KELUARKAN JEMURAN</span>
               </button>
               
               <button @click="setManualPosition('Di Dalam')" :disabled="settings.is_auto_mode" 
                       class="w-full py-6 bg-gradient-to-br from-rose-500 via-rose-600 to-red-700 hover:from-rose-600 hover:to-red-800 text-white disabled:from-slate-100 disabled:to-slate-200 disabled:text-slate-400 font-black text-xl rounded-[1.5rem] shadow-xl hover:shadow-rose-500/40 disabled:shadow-inner transition-all duration-300 focus:ring-4 ring-rose-200 outline-none transform active:scale-[0.98] disabled:active:scale-100 flex justify-center items-center gap-4 group">
                  <div class="p-2 bg-white/20 rounded-xl group-hover:scale-110 transition-transform">
                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                  </div>
                  <span>TARIK MASUK JEMURAN</span>
               </button>
            </div>
        </div>
        </template>

  </div>
</template>

<script>
import axios from 'axios';
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

export default {
  emits: ['toast'],
  data() {
    return {
      settings: { is_auto_mode: true },
      latestData: {},
      historyData: [],
      isLoading: true,
      chart: null,
      // Animated counters
      displayLdr: 0,
      displayRain: 0,
      targetLdr: 0,
      targetRain: 0,
    }
  },
  computed: {
    clotheslineStatus() {
      return this.settings.is_auto_mode ? this.latestData.clothesline_status : this.settings.manual_position;
    },
    summaryStats() {
      const logs = this.historyData;
      if (!logs.length) {
        return [
          { label: 'Rata-rata LDR', value: '—', unit: '', emoji: '☀️', bgClass: 'bg-amber-50 border border-amber-100', valueClass: 'text-amber-600' },
          { label: 'Rata-rata Hujan', value: '—', unit: '', emoji: '🌧️', bgClass: 'bg-blue-50 border border-blue-100', valueClass: 'text-blue-600' },
          { label: 'Total Pergerakan', value: '0', unit: 'kali', emoji: '⚙️', bgClass: 'bg-slate-50 border border-slate-100', valueClass: 'text-slate-700' },
          { label: 'Evakuasi Darurat', value: '0', unit: 'kali', emoji: '🚨', bgClass: 'bg-rose-50 border border-rose-100', valueClass: 'text-rose-600' },
        ];
      }
      const avgLdr = Math.round(logs.reduce((a, l) => a + l.ldr_value, 0) / logs.length);
      const avgRain = Math.round(logs.reduce((a, l) => a + l.rain_percentage, 0) / logs.length);
      const movements = logs.filter((l, i) => i > 0 && l.clothesline_status !== logs[i-1].clothesline_status).length;
      const emergencies = logs.filter(l => l.weather_condition.toLowerCase().includes('hujan')).length;
      return [
        { label: 'Rata-rata LDR', value: avgLdr, unit: '%', emoji: '☀️', bgClass: 'bg-amber-50 border border-amber-100', valueClass: 'text-amber-600' },
        { label: 'Rata-rata Hujan', value: avgRain, unit: '%', emoji: '🌧️', bgClass: 'bg-blue-50 border border-blue-100', valueClass: 'text-blue-600' },
        { label: 'Total Pergerakan', value: movements, unit: 'kali', emoji: '⚙️', bgClass: 'bg-slate-50 border border-slate-100', valueClass: 'text-slate-700' },
        { label: 'Evakuasi Darurat', value: emergencies, unit: 'kali', emoji: '🚨', bgClass: 'bg-rose-50 border border-rose-100', valueClass: 'text-rose-600' },
      ];
    }
  },
  mounted() {
    this.fetchData();
    this.polling = setInterval(this.fetchData, 2000);
  },
  unmounted() {
    clearInterval(this.polling);
    if (this.chart) this.chart.destroy();
  },
  methods: {
    animateCounter(prop, target) {
      const start = this[prop];
      const diff = target - start;
      const duration = 800;
      const startTime = performance.now();
      const step = (timestamp) => {
        const progress = Math.min((timestamp - startTime) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3); // Ease-out cubic
        this[prop] = Math.round(start + diff * eased);
        if (progress < 1) requestAnimationFrame(step);
      };
      requestAnimationFrame(step);
    },
    async fetchData() {
      try {
        const response = await axios.get('/api/dashboard-data');
        if(response.data) {
          this.settings = response.data.setting;
          this.latestData = response.data.latestData || {};
          this.historyData = response.data.history || [];
          this.isLoading = false;

          // Animate counters
          const newLdr = this.latestData.ldr_value || 0;
          const newRain = this.latestData.rain_percentage || 0;
          if (newLdr !== this.targetLdr) { this.targetLdr = newLdr; this.animateCounter('displayLdr', newLdr); }
          if (newRain !== this.targetRain) { this.targetRain = newRain; this.animateCounter('displayRain', newRain); }

          // Update chart
          this.$nextTick(() => { this.updateChart(); });
        }
      } catch (error) { console.error(error); this.isLoading = false; }
    },
    updateChart() {
      const canvas = this.$refs.sensorChart;
      if (!canvas) return;

      const logs = [...this.historyData].reverse();
      const labels = logs.map((l) => {
        if (!l.created_at) return '';
        return new Date(l.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
      });
      const ldrData = logs.map(l => l.ldr_value);
      const rainData = logs.map(l => l.rain_percentage);

      if (this.chart) {
        this.chart.data.labels = labels;
        this.chart.data.datasets[0].data = ldrData;
        this.chart.data.datasets[1].data = rainData;
        this.chart.update('none');
        return;
      }

      const ctx = canvas.getContext('2d');
      const ldrGradient = ctx.createLinearGradient(0, 0, 0, 260);
      ldrGradient.addColorStop(0, 'rgba(251, 191, 36, 0.35)');
      ldrGradient.addColorStop(1, 'rgba(251, 191, 36, 0.01)');
      const rainGradient = ctx.createLinearGradient(0, 0, 0, 260);
      rainGradient.addColorStop(0, 'rgba(99, 102, 241, 0.35)');
      rainGradient.addColorStop(1, 'rgba(99, 102, 241, 0.01)');

      this.chart = new Chart(ctx, {
        type: 'line',
        data: {
          labels,
          datasets: [
            {
              label: 'Cahaya LDR (%)',
              data: ldrData,
              borderColor: '#f59e0b',
              backgroundColor: ldrGradient,
              borderWidth: 3,
              fill: true,
              tension: 0.4,
              pointBackgroundColor: '#f59e0b',
              pointBorderColor: '#fff',
              pointBorderWidth: 2,
              pointRadius: 5,
              pointHoverRadius: 8,
            },
            {
              label: 'Volume Hujan (%)',
              data: rainData,
              borderColor: '#6366f1',
              backgroundColor: rainGradient,
              borderWidth: 3,
              fill: true,
              tension: 0.4,
              pointBackgroundColor: '#6366f1',
              pointBorderColor: '#fff',
              pointBorderWidth: 2,
              pointRadius: 5,
              pointHoverRadius: 8,
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          interaction: { intersect: false, mode: 'index' },
          plugins: {
            legend: {
              position: 'bottom',
              labels: { usePointStyle: true, padding: 20, font: { weight: 'bold', size: 11 } }
            },
            tooltip: {
              backgroundColor: 'rgba(15, 23, 42, 0.9)',
              titleFont: { weight: 'bold' },
              bodyFont: { size: 13 },
              cornerRadius: 12,
              padding: 14,
            }
          },
          scales: {
            x: {
              grid: { display: false },
              ticks: { font: { weight: 'bold', size: 10 }, color: '#94a3b8' }
            },
            y: {
              min: 0, max: 100,
              grid: { color: 'rgba(226,232,240,0.5)' },
              ticks: { font: { weight: 'bold', size: 10 }, color: '#94a3b8', callback: v => v + '%' }
            }
          }
        }
      });
    },
    async toggleAutoMode() {
      this.settings.is_auto_mode = !this.settings.is_auto_mode;
      await axios.post('/api/update-setting', { is_auto_mode: this.settings.is_auto_mode });
      this.$emit('toast', {
        type: this.settings.is_auto_mode ? 'success' : 'info',
        title: this.settings.is_auto_mode ? 'Mode Otomatis Aktif' : 'Manual Override Aktif',
        message: this.settings.is_auto_mode ? 'AI kini mengendalikan rel motor berdasarkan sensor.' : 'Anda sekarang mengontrol jemuran secara manual.'
      });
      this.fetchData();
    },
    async setManualPosition(position) {
      if (this.settings.is_auto_mode) return;
      this.settings.manual_position = position;
      await axios.post('/api/update-setting', { manual_position: position });
      this.$emit('toast', {
        type: 'success',
        title: 'Perintah Terkirim',
        message: `Jemuran dipindahkan ke posisi: ${position}`
      });
      this.fetchData();
    }
  }
}
</script>

<style>
@keyframes slide {
  0% { background-position: 0 0; }
  100% { background-position: 20px 20px; }
}
</style>
