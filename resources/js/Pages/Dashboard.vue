<template>
  <div class="space-y-8 pb-10 relative">
    
    <!-- Loading Skeleton -->
    <div v-if="isLoading" class="space-y-8 animate-pulse">
       <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
          <div class="xl:col-span-2 bg-white/50 rounded-[2.5rem] h-[340px]"></div>
          <div class="bg-white/50 rounded-[2.5rem] h-[340px]"></div>
       </div>
       <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <div class="bg-white/50 rounded-[2rem] h-36" v-for="n in 4" :key="n"></div>
       </div>
       <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <div class="bg-white/50 rounded-[2.5rem] h-[380px]"></div>
          <div class="bg-white/50 rounded-[2.5rem] h-[380px]"></div>
       </div>
    </div>

    <!-- Interface -->
    <div v-else class="space-y-8">
      
      <!-- Top Row -->
      <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        
        <!-- Hero Banner Glassmorphic -->
        <div class="xl:col-span-2 relative bg-white/40 backdrop-blur-3xl rounded-[2.5rem] p-8 md:p-12 shadow-[0_8px_32px_rgba(0,0,0,0.04)] border border-white overflow-hidden group animation-fade-in transition-all duration-500 hover:shadow-[0_16px_48px_rgba(0,0,0,0.08)]">
           <!-- Animated background blobs -->
           <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-400/20 rounded-full blur-[80px] animate-blob"></div>
           <div class="absolute top-10 -right-20 w-80 h-80 bg-indigo-400/20 rounded-full blur-[80px] animate-blob animation-delay-2000"></div>
           <div class="absolute -bottom-40 left-20 w-96 h-96 bg-cyan-300/20 rounded-full blur-[80px] animate-blob animation-delay-4000"></div>

           <div class="flex flex-col md:flex-row items-center gap-10 md:gap-14 relative z-10 w-full justify-between h-full">
              <div class="w-full text-center md:text-left flex flex-col justify-center">
                <div class="inline-flex items-center space-x-2 bg-white/60 backdrop-blur-md px-4 py-1.5 rounded-full text-[10px] sm:text-xs font-black tracking-[0.2em] mb-6 border border-white/50 shadow-sm text-slate-500 max-w-max mx-auto md:mx-0">
                  <span class="w-2 h-2 rounded-full" :class="settings.is_auto_mode ? 'bg-emerald-400 animate-pulse' : 'bg-rose-400'"></span>
                  <span>STATUS POSISI AKTUAL</span>
                </div>
                
                <h3 class="text-4xl md:text-6xl lg:text-7xl font-black mb-4 tracking-tighter text-slate-800 relative inline-block transition-all duration-300">
                  <span class="bg-clip-text text-transparent bg-gradient-to-r from-slate-800 via-indigo-900 to-slate-800 drop-shadow-sm">
                    {{ settings.is_auto_mode ? latestData.clothesline_status : settings.manual_position || 'Memuat...' }}
                  </span>
                </h3>
                
                <div class="flex items-center justify-center md:justify-start gap-3 mb-8">
                   <div class="px-3 py-1 bg-slate-100 rounded-lg text-slate-600 text-sm font-bold border border-slate-200 flex items-center gap-2">
                     <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                     {{ settings.is_auto_mode ? 'Kecerdasan Buatan (Auto)' : 'Manual Override Aktif' }}
                   </div>
                </div>
                
                <div class="flex justify-center md:justify-start">
                   <div class="bg-white/80 backdrop-blur-xl px-6 py-4 rounded-2xl border border-white shadow-[0_4px_16px_rgba(0,0,0,0.03)] flex items-center gap-4 group-hover:scale-105 transition-transform duration-500">
                      <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-2xl shadow-inner border border-indigo-100/50">
                        {{ latestData.weather_condition === 'Hujan' ? '🌧️' : latestData.weather_condition === 'Cerah' ? '☀️' : '⛅' }}
                      </div>
                      <div>
                         <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.2em] mb-0.5">Prediksi Cuaca</p>
                         <p class="text-xl font-extrabold tracking-tight text-slate-700">{{ latestData.weather_condition || 'Memuat...' }}</p>
                      </div>
                   </div>
                </div>
              </div>

              <!-- 3D Inspired Weather Orb -->
              <div class="flex-shrink-0 relative group perspective-1000 mt-6 md:mt-0">
                 <div class="absolute inset-0 rounded-full blur-[50px] animate-pulse transition-colors duration-1000" 
                      :class="clotheslineStatus === 'Di Dalam' ? 'bg-slate-300' : 'bg-amber-400/80'"></div>
                 
                 <div class="w-48 h-48 md:w-56 md:h-56 rounded-full relative transform-style-3d transition-transform duration-1000 hover:rotate-y-12 hover:rotate-x-12 z-10 flex items-center justify-center text-8xl md:text-9xl shadow-2xl"
                      :class="clotheslineStatus === 'Di Dalam' ? 'bg-gradient-to-br from-slate-100 to-slate-200 border-4 border-white' : 'bg-gradient-to-br from-yellow-300 to-orange-400 border-4 border-yellow-200'">
                    <span class="transform drop-shadow-2xl hover:scale-110 transition-transform duration-500">{{ clotheslineStatus === 'Di Dalam' ? '☁️' : '☀️' }}</span>
                    <!-- Shine Effect -->
                    <div class="absolute inset-0 rounded-full bg-gradient-to-tr from-transparent via-white/40 to-white/70 opacity-50 pointer-events-none mix-blend-overlay z-20"></div>
                 </div>
              </div>
           </div>
        </div>

        <!-- Radial Gauges (Sensor Realtime) -->
        <div class="bg-white/40 backdrop-blur-3xl rounded-[2.5rem] p-8 shadow-[0_8px_32px_rgba(0,0,0,0.04)] border border-white flex flex-col items-center justify-center relative overflow-hidden animation-fade-in delay-100">
          <h3 class="text-slate-400 font-extrabold text-[11px] uppercase tracking-[0.2em] mb-8 text-center relative z-10 bg-white/50 px-4 py-1.5 rounded-full border border-white/60">Pembacaan Fisik</h3>
          
          <div class="flex flex-col gap-8 w-full justify-center items-center h-full pb-4">
             
             <div class="flex w-full justify-around items-center">
                <!-- Circular LDR Gauge -->
                <div class="relative flex flex-col items-center group cursor-default">
                  <svg class="w-32 h-32 md:w-36 md:h-36 transform -rotate-90 drop-shadow-md" viewBox="0 0 100 100">
                    <!-- Track -->
                    <circle cx="50" cy="50" r="40" stroke="rgba(245, 158, 11, 0.15)" stroke-width="8" fill="none" class="transition-all duration-300"></circle>
                    <!-- Progress -->
                    <circle cx="50" cy="50" r="40" class="text-amber-500 group-hover:text-amber-400 transition-all duration-1000 ease-out" stroke="currentColor" stroke-width="8" fill="none" stroke-linecap="round" :stroke-dasharray="251.2" :stroke-dashoffset="251.2 - (251.2 * displayLdr / 100)"></circle>
                  </svg>
                  <div class="absolute inset-0 flex flex-col items-center justify-center -mt-6">
                     <span class="text-2xl mt-1 text-amber-500 animate-bounce-slow">☀️</span>
                     <span class="text-2xl font-black text-slate-700 tracking-tighter relative">{{ displayLdr }}<span class="text-xs font-bold absolute top-1 -right-3">%</span></span>
                  </div>
                  <span class="mt-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider bg-white/60 px-3 py-1 rounded-lg border border-white">LDR Matahari</span>
                </div>

                <!-- Circular Rain Gauge -->
                <div class="relative flex flex-col items-center group cursor-default">
                  <svg class="w-32 h-32 md:w-36 md:h-36 transform -rotate-90 drop-shadow-md" viewBox="0 0 100 100">
                    <!-- Track -->
                    <circle cx="50" cy="50" r="40" stroke="rgba(99, 102, 241, 0.15)" stroke-width="8" fill="none"></circle>
                    <!-- Progress -->
                    <circle cx="50" cy="50" r="40" class="text-indigo-500 group-hover:text-indigo-400 transition-all duration-1000 ease-out" stroke="currentColor" stroke-width="8" fill="none" stroke-linecap="round" :stroke-dasharray="251.2" :stroke-dashoffset="251.2 - (251.2 * displayRain / 100)"></circle>
                  </svg>
                  <div class="absolute inset-0 flex flex-col items-center justify-center -mt-6">
                     <span class="text-2xl mt-1 text-indigo-500 animate-bounce-slow" style="animation-delay: 1s">🌧️</span>
                     <span class="text-2xl font-black text-slate-700 tracking-tighter relative">{{ displayRain }}<span class="text-xs font-bold absolute top-1 -right-3">%</span></span>
                  </div>
                  <span class="mt-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider bg-white/60 px-3 py-1 rounded-lg border border-white">Intensitas Air</span>
                </div>
             </div>
             
          </div>
        </div>
      </div>

      <!-- Summary Stats Row -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
         <div v-for="(stat, index) in summaryStats" :key="stat.label" 
              class="bg-white/40 backdrop-blur-2xl rounded-[2rem] p-6 border border-white shadow-[0_4px_24px_rgba(0,0,0,0.02)] group hover:-translate-y-2 transition-all duration-500 cursor-default animation-fade-in-up hover:shadow-[0_12px_36px_rgba(0,0,0,0.06)]"
              :style="{ animationDelay: `${(index + 2) * 100}ms` }">
            <div class="flex items-center gap-4 mb-4">
               <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl shadow-sm border group-hover:scale-110 transition-transform duration-300" :class="stat.bgClass">{{ stat.emoji }}</div>
               <span class="text-[10px] md:text-xs font-black uppercase tracking-widest text-slate-400 leading-tight">{{ stat.label }}</span>
            </div>
            <p class="text-4xl md:text-5xl font-black tracking-tighter tabular-nums drop-shadow-sm transition-colors duration-300" :class="stat.valueClass">{{ stat.value }}<span class="text-base font-bold ml-1 opacity-70">{{ stat.unit }}</span></p>
         </div>
      </div>

      <!-- Chart + Control Row -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
         
         <!-- Neumorphic Main Switch -->
         <div class="bg-white/40 backdrop-blur-3xl rounded-[2.5rem] p-8 md:p-10 shadow-[0_8px_32px_rgba(0,0,0,0.04)] border border-white flex flex-col items-center justify-center min-h-[340px] relative overflow-hidden animation-fade-in-up delay-600 group">
             <!-- Interactive Glow Background -->
             <div class="absolute inset-0 transition-opacity duration-700 opacity-20 pointer-events-none"
                  :class="settings.is_auto_mode ? 'bg-gradient-to-tr from-emerald-300 to-emerald-50' : 'bg-gradient-to-tr from-rose-300 to-rose-50'"></div>
             
             <div class="inline-flex items-center space-x-2 bg-white/60 backdrop-blur-md px-4 py-1.5 rounded-full text-[10px] font-black tracking-[0.2em] mb-6 border border-white/50 relative z-10 text-slate-500">
                SISTEM PUSAT
             </div>
             
             <p class="text-center text-sm text-slate-500 mb-10 max-w-sm font-medium relative z-10">Alihkan ke Mode Manual untuk mengambil kendali rel motor secara penuh dari Kecerdasan Buatan.</p>
             
             <div class="flex flex-col items-center justify-center relative z-10 w-full">
               <button @click="toggleAutoMode" 
                       class="relative flex h-24 w-48 items-center rounded-full transition-all duration-500 focus:outline-none cursor-pointer border-[8px] overflow-hidden hover:scale-105 active:scale-95"
                       :class="settings.is_auto_mode ? 'bg-emerald-400 border-emerald-100/50 shadow-[inset_0_4px_10px_rgba(0,0,0,0.1),0_0_30px_rgba(16,185,129,0.3)]' : 'bg-slate-200 border-slate-50 shadow-[inset_0_4px_10px_rgba(0,0,0,0.1)]'">
                 
                 <div class="absolute inset-0 bg-black/5 opacity-0 hover:opacity-100 transition-opacity pointer-events-none"></div>

                 <!-- Toggle Knob -->
                 <span class="inline-flex h-16 w-16 transform rounded-full bg-white shadow-[0_4px_12px_rgba(0,0,0,0.2)] transition-transform duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)] items-center justify-center relative z-10"
                       :class="settings.is_auto_mode ? 'translate-x-[7rem]' : 'translate-x-2'">
                    <i v-if="settings.is_auto_mode" class="text-emerald-500 text-2xl font-black filter drop-shadow-sm flex items-center h-full not-italic">🤖</i>
                    <i v-else class="text-slate-400 text-2xl font-black filter drop-shadow-sm flex items-center h-full not-italic">✋</i>
                 </span>
                 
                 <!-- Status Text inside Toggle -->
                 <span class="absolute right-6 font-black text-white/90 text-sm tracking-widest transition-opacity duration-300" :class="settings.is_auto_mode ? 'opacity-100' : 'opacity-0'">AUTO</span>
                 <span class="absolute left-6 font-black text-slate-400 text-sm tracking-widest transition-opacity duration-300" :class="!settings.is_auto_mode ? 'opacity-100' : 'opacity-0'">MANUAL</span>
               </button>
               
               <div class="mt-8 px-6 py-2 rounded-2xl bg-white/50 border border-white backdrop-blur shadow-sm transition-colors duration-500"
                    :class="settings.is_auto_mode ? 'text-emerald-600' : 'text-slate-600'">
                 <span class="font-black text-[1rem] md:text-[1.2rem] tracking-tight"> <!-- fixed sizing to avoid wrap -->
                    {{ settings.is_auto_mode ? 'KENDALI KECERDASAN BUATAN' : 'PENGENDALIAN MANUAL AKTIF'}}
                 </span>
               </div>
             </div>
         </div>

         <!-- Manual Control Buttons -->
         <div class="bg-white/40 backdrop-blur-3xl rounded-[2.5rem] p-8 md:p-10 shadow-[0_8px_32px_rgba(0,0,0,0.04)] border border-white relative flex flex-col justify-center min-h-[340px] animation-fade-in-up delay-700">
             <div class="flex justify-between items-center mb-6">
               <h3 class="text-slate-400 font-extrabold text-[11px] uppercase tracking-[0.2em] bg-white/60 px-4 py-1.5 rounded-full border border-white/60">Eksekusi Manual</h3>
               <span class="relative flex h-3 w-3" v-if="!settings.is_auto_mode">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 bg-rose-400"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
               </span>
             </div>

             <!-- Pending Command Indicator -->
             <transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
               <div v-if="pendingCommand" class="mb-4 flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-2xl text-xs font-bold">
                 <svg class="w-4 h-4 animate-spin text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                 <span>Mengirim perintah ke ESP32... Tunggu response berikutnya (~10 detik)</span>
               </div>
             </transition>
             
             <!-- Glow Effect container when manual mode -->
             <div class="grid grid-cols-1 gap-5 relative z-10">
                <div v-if="!settings.is_auto_mode" class="absolute -inset-4 bg-rose-400/20 blur-2xl rounded-[3rem] animate-pulse pointer-events-none -z-10"></div>
                
                <button @click="pushCommand('move_out')" :disabled="settings.is_auto_mode || isSendingCommand"
                        class="relative overflow-hidden w-full py-6 md:py-7 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-400 hover:to-indigo-500 text-white disabled:from-slate-200 disabled:to-slate-200 disabled:text-slate-400 font-black text-lg md:text-xl rounded-[1.5rem] shadow-[0_8px_20px_rgba(79,70,229,0.3)] hover:shadow-[0_12px_30px_rgba(79,70,229,0.4)] disabled:shadow-none transition-all duration-300 outline-none transform active:scale-[0.98] disabled:active:scale-100 flex justify-center items-center gap-4 group">
                   <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 -translate-x-[150%] skew-x-[-20deg] group-hover:animate-shine disabled:hidden"></div>
                   <div class="w-10 h-10 bg-white/20 rounded-xl group-hover:scale-110 transition-transform flex items-center justify-center backdrop-blur-sm shadow-inner border border-white/20">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                   </div>
                   <span class="tracking-wide text-sm md:text-xl drop-shadow-md">KELUARKAN JEMURAN</span>
                </button>
                
                <button @click="pushCommand('move_in')" :disabled="settings.is_auto_mode || isSendingCommand"
                        class="relative overflow-hidden w-full py-6 md:py-7 bg-gradient-to-r from-rose-500 to-red-600 hover:from-rose-400 hover:to-red-500 text-white disabled:from-slate-200 disabled:to-slate-200 disabled:text-slate-400 font-black text-lg md:text-xl rounded-[1.5rem] shadow-[0_8px_20px_rgba(244,63,94,0.3)] hover:shadow-[0_12px_30px_rgba(244,63,94,0.4)] disabled:shadow-none transition-all duration-300 outline-none transform active:scale-[0.98] disabled:active:scale-100 flex justify-center items-center gap-4 group">
                   <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 -translate-x-[150%] skew-x-[-20deg] group-hover:animate-shine disabled:hidden"></div>
                   <div class="w-10 h-10 bg-white/20 rounded-xl group-hover:scale-110 transition-transform flex items-center justify-center backdrop-blur-sm shadow-inner border border-white/20">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                   </div>
                   <span class="tracking-wide text-sm md:text-xl drop-shadow-md">TARIK MASUK JEMURAN</span>
                </button>
             </div>
         </div>
      </div>

      <!-- Live Chart Full Width Row -->
      <div class="bg-white/50 backdrop-blur-3xl rounded-[2.5rem] p-8 md:p-10 shadow-[0_8px_32px_rgba(0,0,0,0.04)] border border-white relative overflow-hidden animation-fade-in-up delay-800 group hover:shadow-[0_16px_48px_rgba(0,0,0,0.06)] transition-shadow duration-500">
          <div class="flex justify-between items-center mb-8">
             <h3 class="text-slate-500 font-extrabold text-[10px] md:text-xs uppercase tracking-[0.2em] flex items-center gap-3">
               <span class="p-2 bg-indigo-50 text-indigo-500 rounded-xl border border-indigo-100"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg></span>
               Grafik Tren Sensor
             </h3>
             <span class="text-[9px] md:text-[10px] bg-white border border-slate-200 text-slate-500 px-3 py-1.5 rounded-full font-bold shadow-sm flex items-center gap-2">
               <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
               REKAM JEJAK
             </span>
          </div>
          <div class="relative w-full rounded-2xl bg-white/30 backdrop-blur-sm p-4 border border-white/40" style="height: 320px;">
             <canvas ref="sensorChart"></canvas>
          </div>
      </div>
      
    </div>
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
      // Command Queue
      pendingCommand: null,   // Perintah yang sedang menunggu dieksekusi ESP32
      isSendingCommand: false,
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
          { label: 'Rata-rata LDR', value: '—', unit: '', emoji: '☀️', bgClass: 'bg-amber-50 border-amber-100 text-amber-500', valueClass: 'text-amber-500' },
          { label: 'Rata-rata Hujan', value: '—', unit: '', emoji: '🌧️', bgClass: 'bg-indigo-50 border-indigo-100 text-indigo-500', valueClass: 'text-indigo-500' },
          { label: 'Total Pergerakan', value: '0', unit: 'kali', emoji: '⚙️', bgClass: 'bg-slate-50 border-slate-200 text-slate-500', valueClass: 'text-slate-600' },
          { label: 'Evakuasi Darurat', value: '0', unit: 'kali', emoji: '🚨', bgClass: 'bg-rose-50 border-rose-100 text-rose-500', valueClass: 'text-rose-500' },
        ];
      }
      const avgLdr = Math.round(logs.reduce((a, l) => a + l.ldr_value, 0) / logs.length);
      const avgRain = Math.round(logs.reduce((a, l) => a + l.rain_percentage, 0) / logs.length);
      const movements = logs.filter((l, i) => i > 0 && l.clothesline_status !== logs[i-1].clothesline_status).length;
      const emergencies = logs.filter(l => l.weather_condition.toLowerCase().includes('hujan')).length;
      return [
        { label: 'Rata-rata LDR', value: avgLdr, unit: '%', emoji: '☀️', bgClass: 'bg-amber-50 border-amber-100 text-amber-500', valueClass: 'text-amber-500' },
        { label: 'Rata-rata Hujan', value: avgRain, unit: '%', emoji: '🌧️', bgClass: 'bg-indigo-50 border-indigo-100 text-indigo-500', valueClass: 'text-indigo-500' },
        { label: 'Total Pergerakan', value: movements, unit: 'kali', emoji: '⚙️', bgClass: 'bg-slate-50 border-slate-200 text-slate-500', valueClass: 'text-slate-600' },
        { label: 'Evakuasi Darurat', value: emergencies, unit: 'kali', emoji: '🚨', bgClass: 'bg-rose-50 border-rose-100 text-rose-500', valueClass: 'text-rose-500' },
      ];
    }
  },
  mounted() {
    this.fetchData();
    this.polling = setInterval(this.fetchData, 3000);
  },
  unmounted() {
    clearInterval(this.polling);
    if (this.chart) this.chart.destroy();
  },
  methods: {
    animateCounter(prop, target) {
      const start = this[prop];
      const diff = target - start;
      const duration = 1200; // Smoother longer animation
      const startTime = performance.now();
      const step = (timestamp) => {
        const progress = Math.min((timestamp - startTime) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 4); // Quartic ease-out
        this[prop] = Math.round(start + diff * eased);
        if (progress < 1) requestAnimationFrame(step);
      };
      requestAnimationFrame(step);
    },
    async fetchData() {
      try {
        const response = await axios.get('/api/dashboard-data');
        if(response.data) {
          this.settings    = response.data.setting;
          this.latestData  = response.data.latestData || {};
          this.historyData = response.data.history || [];
          this.isLoading   = false;

          // Sinkronisasi status pending command dari server
          this.pendingCommand = response.data.pendingCommand || null;
          // Jika tidak ada pending command lagi → command sudah dieksekusi ESP32
          if (!this.pendingCommand) this.isSendingCommand = false;

          // Animate counters
          const newLdr  = this.latestData.ldr_value || 0;
          const newRain = this.latestData.rain_percentage || 0;
          if (newLdr  !== this.targetLdr)  { this.targetLdr  = newLdr;  this.animateCounter('displayLdr',  newLdr);  }
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
      const ldrGradient = ctx.createLinearGradient(0, 0, 0, 300);
      ldrGradient.addColorStop(0, 'rgba(245, 158, 11, 0.4)');
      ldrGradient.addColorStop(1, 'rgba(245, 158, 11, 0)');
      
      const rainGradient = ctx.createLinearGradient(0, 0, 0, 300);
      rainGradient.addColorStop(0, 'rgba(99, 102, 241, 0.4)');
      rainGradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

      this.chart = new Chart(ctx, {
        type: 'line',
        data: {
          labels,
          datasets: [
            {
              label: 'LDR',
              data: ldrData,
              borderColor: '#f59e0b',
              backgroundColor: ldrGradient,
              borderWidth: 3.5,
              fill: true,
              cubicInterpolationMode: 'monotone',
              tension: 0.4,
              pointBackgroundColor: '#fff',
              pointBorderColor: '#f59e0b',
              pointBorderWidth: 2.5,
              pointRadius: 4,
              pointHoverRadius: 7,
            },
            {
              label: 'Hujan',
              data: rainData,
              borderColor: '#6366f1',
              backgroundColor: rainGradient,
              borderWidth: 3.5,
              fill: true,
              cubicInterpolationMode: 'monotone',
              tension: 0.4,
              pointBackgroundColor: '#fff',
              pointBorderColor: '#6366f1',
              pointBorderWidth: 2.5,
              pointRadius: 4,
              pointHoverRadius: 7,
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          interaction: { intersect: false, mode: 'index' },
          plugins: {
            legend: {
              position: 'top',
              align: 'end',
              labels: { usePointStyle: true, padding: 20, font: { weight: 'bold', size: 12 }, color: '#64748b' }
            },
            tooltip: {
              backgroundColor: 'rgba(255, 255, 255, 0.95)',
              titleColor: '#0f172a',
              bodyColor: '#334155',
              borderColor: 'rgba(226, 232, 240, 1)',
              borderWidth: 1,
              titleFont: { weight: 'bold', size: 13 },
              bodyFont: { weight: 'bold', size: 13 },
              cornerRadius: 16,
              padding: 16,
              boxPadding: 6,
              usePointStyle: true,
              callbacks: {
                label: function(context) { return ` ${context.dataset.label} : ${context.parsed.y}%`; }
              }
            }
          },
          scales: {
            x: {
              grid: { display: false },
              border: { display: false },
              ticks: { font: { weight: 'bold', size: 11 }, color: '#94a3b8', padding: 10 }
            },
            y: {
              min: 0, max: 100,
              grid: { color: 'rgba(226,232,240,0.6)', borderDash: [4, 4] },
              border: { display: false },
              ticks: { font: { weight: 'bold', size: 11 }, color: '#94a3b8', callback: v => v + '%', padding: 10 }
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
        title: this.settings.is_auto_mode ? 'Kecerdasan Buatan Aktif' : 'Sistem Manual Diambil Alih',
        message: this.settings.is_auto_mode ? 'AI kini memantau cuaca dan mengendalikan rel motor.' : 'Anda memegang kendali penuh atas posisi jemuran.'
      });
      this.fetchData();
    },
    /**
     * Push perintah ke command queue — ESP32 akan eksekusi dalam ≤10 detik.
     * Jauh lebih responsif dari sebelumnya (dulu harus tunggu interval polling).
     */
    async pushCommand(action) {
      if (this.settings.is_auto_mode || this.isSendingCommand) return;
      this.isSendingCommand = true;

      const labels = {
        move_out: 'Keluarkan Jemuran',
        move_in:  'Tarik Masuk Jemuran',
      };

      try {
        await axios.post('/api/device/command', { command: action });
        this.$emit('toast', {
          type: 'info',
          title: '📡 Perintah Dikirim',
          message: `Perintah "${labels[action]}" sedang menunggu ESP32. Akan dieksekusi dalam ≤10 detik.`
        });
        // Segera refresh untuk tampilkan badge "Mengirim perintah..."
        this.fetchData();
      } catch (error) {
        this.isSendingCommand = false;
        this.$emit('toast', {
          type: 'error',
          title: 'Gagal Kirim Perintah',
          message: 'Tidak dapat menghubungi server. Cek koneksi Anda.'
        });
      }
    }
  }
}
</script>

<style>
/* Custom Keyframes for Premium Polish */
@keyframes blob {
  0% { transform: translate(0px, 0px) scale(1); }
  33% { transform: translate(30px, -50px) scale(1.1); }
  66% { transform: translate(-20px, 20px) scale(0.9); }
  100% { transform: translate(0px, 0px) scale(1); }
}
.animate-blob {
  animation: blob 10s infinite alternate cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes fade-in-up {
  0% { opacity: 0; transform: translateY(30px) scale(0.98); }
  100% { opacity: 1; transform: translateY(0) scale(1); }
}
.animation-fade-in-up {
  opacity: 0;
  animation: fade-in-up 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes fade-in {
  0% { opacity: 0; transform: scale(0.98); }
  100% { opacity: 1; transform: scale(1); }
}
.animation-fade-in {
  opacity: 0;
  animation: fade-in 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes shine {
  100% { transform: translateX(150%) skew(-20deg); }
}
.animate-shine {
  animation: shine 1.5s cubic-bezier(0.4, 0, 0.2, 1) infinite;
}

@keyframes bounce-slow {
  0%, 100% { transform: translateY(-5%); animation-timing-function: cubic-bezier(0.8,0,1,1); }
  50% { transform: translateY(5%); animation-timing-function: cubic-bezier(0,0,0.2,1); }
}
.animate-bounce-slow {
  animation: bounce-slow 3s infinite;
}

/* Delays */
.delay-100 { animation-delay: 100ms; }
.delay-200 { animation-delay: 200ms; }
.delay-300 { animation-delay: 300ms; }
.delay-400 { animation-delay: 400ms; }
.delay-500 { animation-delay: 500ms; }
.delay-600 { animation-delay: 600ms; }
.delay-700 { animation-delay: 700ms; }
.delay-800 { animation-delay: 800ms; }

/* 3D Perspective Utils */
.perspective-1000 { perspective: 1000px; }
.transform-style-3d { transform-style: preserve-3d; }
.rotate-y-12 { transform: rotateY(12deg); }
.rotate-x-12 { transform: rotateX(12deg); }
</style>
