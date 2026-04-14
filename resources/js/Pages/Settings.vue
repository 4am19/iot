<template>
  <div class="max-w-5xl mx-auto space-y-12 pb-12">
     <!-- Header -->
     <div class="bg-white/60 backdrop-blur-xl p-8 rounded-[2rem] border border-white shadow-sm flex flex-col xl:flex-row gap-6 justify-between items-center relative overflow-hidden">
        <div class="absolute right-0 top-0 w-64 h-64 bg-blue-100/50 rounded-full blur-[60px] pointer-events-none -mr-10 -mt-10"></div>
        <div class="relative z-10 text-center xl:text-left flex-1">
           <h3 class="text-3xl md:text-4xl font-black text-slate-800 tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-indigo-700 to-blue-600">Sistem & Kalibrasi</h3>
           <p class="text-slate-500 mt-3 font-medium text-lg max-w-xl">Atur identitas sistem dan sensitivitas mata AI secara presisi.</p>
        </div>

        <div class="relative z-10 bg-white/80 p-5 rounded-2xl border border-slate-100 shadow-sm w-full xl:w-auto min-w-[320px]">
           <label class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2 block">Identitas Administrator</label>
           <div class="flex gap-2">
              <input type="text" v-model="settings.owner_name" @blur="saveSettings" @keyup.enter="saveSettings" placeholder="Nama Anda" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all">
              <button @click="saveSettings" class="bg-blue-50 text-blue-600 px-4 py-2.5 rounded-xl font-black hover:bg-blue-100 transition-colors active:scale-95 shadow-sm border border-blue-100">SIMPAN</button>
           </div>
        </div>
     </div>

     <!-- Loading Skeleton -->
     <div v-if="isLoading" class="grid grid-cols-1 lg:grid-cols-2 gap-8 animate-pulse">
        <div class="bg-white/50 rounded-[2.5rem] h-96"></div>
        <div class="bg-white/50 rounded-[2.5rem] h-96"></div>
     </div>

     <template v-else>
     <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- LDR Setting -->
        <div class="bg-white/80 backdrop-blur-xl p-10 rounded-[2.5rem] shadow-[0_8px_30px_rgba(0,0,0,0.03)] border border-white relative overflow-hidden group transition-all hover:shadow-[0_8px_40px_rgba(59,130,246,0.15)] flex flex-col h-full">
           <div class="absolute top-0 right-0 w-48 h-48 bg-yellow-200 opacity-20 rounded-full -mr-16 -mt-16 blur-2xl transition-transform duration-700 group-hover:scale-125"></div>
           
           <div class="flex items-center gap-6 mb-8 relative z-10">
              <div class="w-16 h-16 bg-gradient-to-br from-yellow-100 to-amber-100 rounded-[1.25rem] flex items-center justify-center text-4xl shadow-sm border border-yellow-200 rotate-3 group-hover:rotate-6 transition-transform">☀️</div>
              <div>
                 <h4 class="font-extrabold text-2xl text-slate-800 tracking-tight">Fotosensor Cahaya</h4>
                 <div class="inline-flex items-center gap-2 mt-1 px-3 py-1 bg-amber-50 rounded-lg border border-amber-100">
                    <div class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>
                    <p class="text-[11px] font-black text-amber-600 uppercase tracking-widest">Batas Gelap Maksimum</p>
                 </div>
              </div>
           </div>
           
           <div class="mb-4 flex justify-between items-end relative z-10">
              <span class="text-sm font-bold text-slate-400 uppercase tracking-wider">Trigger Rel</span>
              <span class="text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500 drop-shadow-sm tabular-nums">{{ settings.ldr_threshold }}%</span>
           </div>

           <div class="relative py-4 mb-4 z-10">
              <div class="absolute w-full h-4 bg-slate-100 rounded-full inset-y-0 my-auto shadow-inner border border-slate-200"></div>
              <div class="absolute h-4 rounded-full inset-y-0 my-auto bg-gradient-to-r from-yellow-300 to-orange-500 shadow-[0_0_15px_rgba(245,158,11,0.5)] transition-all ease-out" :style="`width: ${settings.ldr_threshold}%`"></div>
              <input type="range" min="0" max="100" v-model.number="settings.ldr_threshold" @change="saveSettings" 
                     class="w-full absolute inset-y-0 my-auto h-4 appearance-none bg-transparent cursor-grab active:cursor-grabbing outline-none z-20 
                            [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-8 [&::-webkit-slider-thumb]:h-8 [&::-webkit-slider-thumb]:bg-white [&::-webkit-slider-thumb]:border-4 [&::-webkit-slider-thumb]:border-orange-500 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:shadow-lg [&::-webkit-slider-thumb]:transition-transform [&::-webkit-slider-thumb]:hover:scale-125">
           </div>
           
           <div class="mt-auto pt-6 bg-gradient-to-br from-amber-50 to-orange-50/50 p-6 rounded-2xl border border-amber-100/50 text-amber-900 leading-relaxed shadow-inner">
             <strong class="text-amber-700 bg-amber-100 px-2 py-1 rounded text-xs uppercase tracking-widest block mb-2 w-max">Cara Kerja AI</strong>
             Jika kadar terik matahari jatuh <span class="font-extrabold bg-amber-200/50 px-1 rounded">di bawah {{ settings.ldr_threshold }}%</span> (Mendung/Malam), jemuran otomatis akan <strong>ditarik masuk</strong>.
           </div>
        </div>

        <!-- Rain Setting -->
        <div class="bg-white/80 backdrop-blur-xl p-10 rounded-[2.5rem] shadow-[0_8px_30px_rgba(0,0,0,0.03)] border border-white relative overflow-hidden group transition-all hover:shadow-[0_8px_40px_rgba(99,102,241,0.15)] flex flex-col h-full">
           <div class="absolute top-0 right-0 w-48 h-48 bg-indigo-200 opacity-20 rounded-full -mr-16 -mt-16 blur-2xl transition-transform duration-700 group-hover:scale-125"></div>
           
           <div class="flex items-center gap-6 mb-8 relative z-10">
              <div class="w-16 h-16 bg-gradient-to-br from-indigo-100 to-blue-100 rounded-[1.25rem] flex items-center justify-center text-4xl shadow-sm border border-indigo-200 -rotate-3 group-hover:-rotate-6 transition-transform">🌧️</div>
              <div>
                 <h4 class="font-extrabold text-2xl text-slate-800 tracking-tight">Sensor Presipitasi</h4>
                 <div class="inline-flex items-center gap-2 mt-1 px-3 py-1 bg-indigo-50 rounded-lg border border-indigo-100">
                    <div class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></div>
                    <p class="text-[11px] font-black text-indigo-600 uppercase tracking-widest">Toleransi Puncak Air</p>
                 </div>
              </div>
           </div>
           
           <div class="mb-4 flex justify-between items-end relative z-10">
              <span class="text-sm font-bold text-slate-400 uppercase tracking-wider">Trigger Rel</span>
              <span class="text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-indigo-600 drop-shadow-sm tabular-nums">{{ settings.rain_threshold }}%</span>
           </div>

           <div class="relative py-4 mb-4 z-10">
              <div class="absolute w-full h-4 bg-slate-100 rounded-full inset-y-0 my-auto shadow-inner border border-slate-200"></div>
              <div class="absolute h-4 rounded-full inset-y-0 my-auto bg-gradient-to-r from-blue-400 to-indigo-600 shadow-[0_0_15px_rgba(99,102,241,0.5)] transition-all ease-out" :style="`width: ${settings.rain_threshold}%`"></div>
              <input type="range" min="0" max="100" v-model.number="settings.rain_threshold" @change="saveSettings" 
                     class="w-full absolute inset-y-0 my-auto h-4 appearance-none bg-transparent cursor-grab active:cursor-grabbing outline-none z-20 
                            [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-8 [&::-webkit-slider-thumb]:h-8 [&::-webkit-slider-thumb]:bg-white [&::-webkit-slider-thumb]:border-4 [&::-webkit-slider-thumb]:border-indigo-500 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:shadow-lg [&::-webkit-slider-thumb]:transition-transform [&::-webkit-slider-thumb]:hover:scale-125">
           </div>
           
           <div class="mt-auto pt-6 bg-gradient-to-br from-indigo-50 to-blue-50/50 p-6 rounded-2xl border border-indigo-100/50 text-indigo-900 leading-relaxed shadow-inner">
             <strong class="text-indigo-700 bg-indigo-100 px-2 py-1 rounded text-xs uppercase tracking-widest block mb-2 w-max">Cara Kerja AI</strong>
             Jika kadar air di atas modul melebihi batas <span class="font-extrabold bg-indigo-200/50 px-1 rounded">{{ settings.rain_threshold }}%</span>, sistem menganggap hujan turun dan <strong>mengamankan jemuran</strong>.
           </div>
        </div>
     </div>

     <!-- Glowing Master Switch Card -->
     <div class="rounded-[2.5rem] shadow-2xl relative overflow-hidden group transition-all duration-500"
          :class="settings.is_auto_mode ? 'bg-gradient-to-br from-slate-900 via-[#1e293b] to-black text-white' : 'bg-gradient-to-br from-rose-600 via-red-600 to-rose-800 text-white'">
        
        <div v-show="settings.is_auto_mode" class="absolute left-1/4 top-1/2 -translate-y-1/2 w-96 h-96 bg-emerald-500/20 rounded-full blur-[80px] animate-[pulse_4s_ease-in-out_infinite]"></div>
        <div v-show="!settings.is_auto_mode" class="absolute right-1/4 top-1/2 -translate-y-1/2 w-96 h-96 bg-yellow-500/30 rounded-full blur-[80px] animate-[pulse_2s_ease-in-out_infinite]"></div>

        <div class="p-10 md:p-12 flex flex-col md:flex-row justify-between items-center relative z-10 gap-8">
           <div class="max-w-xl text-center md:text-left">
              <div class="inline-flex items-center gap-2 mb-4 px-4 py-1.5 rounded-full border bg-white/10 backdrop-blur-md"
                   :class="settings.is_auto_mode ? 'border-emerald-500/30 text-emerald-300' : 'border-yellow-300/30 text-yellow-200'">
                  <div class="w-2.5 h-2.5 rounded-full animate-ping" :class="settings.is_auto_mode ? 'bg-emerald-400' : 'bg-yellow-400'"></div>
                  <span class="text-xs font-black uppercase tracking-[0.2em]">{{ settings.is_auto_mode ? 'Sistem Aktif' : 'Perlu Intervensi' }}</span>
              </div>
              <h4 class="font-black text-4xl lg:text-5xl mb-4 tracking-tight drop-shadow-md">
                 {{ settings.is_auto_mode ? 'Kecerdasan Buatan Menyala' : 'Hak Akses AI Dicabut!' }}
              </h4>
              <p class="text-white/70 text-lg font-medium leading-relaxed">
                 {{ settings.is_auto_mode ? 'ESP32 mengontrol jemuran berdasarkan sensor real-time. Anda bisa bersantai.' : 'Mode manual aktif. Gunakan tombol kontrol dari dashboard untuk mengontrol jemuran.' }}
              </p>
           </div>
           
           <button @click="toggleAutoMode" 
                   class="flex-shrink-0 relative group/btn px-10 py-5 rounded-[2rem] font-black tracking-widest uppercase transition-all duration-500 overflow-hidden text-xl z-20 border-2"
                   :class="settings.is_auto_mode ? 'bg-emerald-500 border-emerald-400 text-white shadow-[0_0_50px_rgba(16,185,129,0.5)] hover:bg-emerald-400 hover:scale-105 active:scale-95' : 'bg-white text-rose-600 border-white shadow-[0_0_50px_rgba(255,255,255,0.3)] hover:bg-slate-50 hover:scale-105 active:scale-95'">
               <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-[150%] skew-x-[-20deg] group-hover/btn:translate-x-[150%] transition-transform duration-700"></div>
               <div class="relative flex items-center justify-center gap-3">
                  <span class="text-3xl">{{ settings.is_auto_mode ? '⚡' : '🛡️' }}</span>
                  <span>{{ settings.is_auto_mode ? 'GABUNG MANUAL' : 'REAKTIVASI AI' }}</span>
               </div>
           </button>
        </div>
     </div>
     <!-- Device API Key -->
     <div class="bg-white/80 backdrop-blur-xl p-8 rounded-[2rem] shadow-[0_8px_30px_rgba(0,0,0,0.03)] border border-slate-100 flex flex-col md:flex-row items-center gap-6 group">
        <div class="w-16 h-16 bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl flex items-center justify-center text-white text-2xl shadow-lg border border-slate-700 flex-shrink-0 group-hover:scale-110 transition-transform">
           <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
        </div>
        <div class="flex-1 text-center md:text-left">
           <h4 class="font-black text-xl text-slate-800 tracking-tight">Otentikasi Mesin (ESP32 API Key)</h4>
           <p class="text-sm font-medium text-slate-500 mt-1">Gunakan kunci rahasia ini sebagai Header <code class="bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded font-mono text-xs font-bold border border-slate-200 shadow-sm">X-API-KEY</code> di firmware C++ Anda.</p>
        </div>
        <div class="w-full md:w-auto relative">
           <input type="text" readonly :value="settings.device_key || 'Generate via Seeder...'" class="w-full md:w-[360px] bg-slate-50 border border-slate-200 text-slate-600 rounded-[1.25rem] pl-5 pr-14 py-4 font-mono font-bold tracking-widest focus:outline-none focus:ring-4 focus:ring-blue-500/10 text-center shadow-inner">
           <button @click="copyApiKey" class="absolute right-2 top-1/2 -translate-y-1/2 p-2.5 bg-white rounded-xl text-slate-400 hover:text-blue-600 shadow-sm border border-slate-100 active:scale-95 transition-all">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
           </button>
        </div>
     </div>
     </template>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  emits: ['toast'],
  data() {
    return {
      isLoading: true,
      settings: {
        is_auto_mode: true,
        ldr_threshold: 50,
        rain_threshold: 5,
        manual_position: 'Terbuka',
        owner_name: 'Administrator'
      }
    }
  },
  mounted() {
    this.fetchData();
    this.polling = setInterval(this.fetchData, 5000);
  },
  unmounted() {
    clearInterval(this.polling);
  },
  methods: {
    async fetchData() {
      try {
        const response = await axios.get('/api/dashboard-data');
        if(response.data) this.settings = response.data.setting;
      } catch (error) { console.error(error); }
      finally { this.isLoading = false; }
    },
    async saveSettings() {
      try {
        await axios.post('/api/update-setting', this.settings);
        this.$emit('toast', { type: 'success', title: 'Tersimpan!', message: 'Kalibrasi sensor berhasil diperbarui.' });
      } catch (error) { 
        this.$emit('toast', { type: 'error', title: 'Gagal!', message: 'Tidak bisa menyimpan pengaturan.' });
      }
    },
    async toggleAutoMode() {
      this.settings.is_auto_mode = !this.settings.is_auto_mode;
      await this.saveSettings();
    },
    copyApiKey() {
       if (this.settings.device_key) {
          navigator.clipboard.writeText(this.settings.device_key);
          this.$emit('toast', { type: 'success', title: 'Tersalin', message: 'API Key disalin ke papan klip.' });
       }
    }
  }
}
</script>
