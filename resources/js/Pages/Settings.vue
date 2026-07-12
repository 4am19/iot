<template>
  <div class="max-w-5xl mx-auto space-y-12 pb-12">
     <!-- Header -->
     <div class="bg-white/60 dark:bg-white/[0.04] backdrop-blur-xl p-6 md:p-8 rounded-3xl md:rounded-[2rem] border border-white dark:border-white/10 shadow-sm dark:shadow-none flex justify-center items-center relative overflow-hidden transition-colors duration-500 text-center">
        <div class="absolute right-0 top-0 w-64 h-64 bg-blue-100/50 dark:bg-indigo-500/10 rounded-full blur-[60px] pointer-events-none -mr-10 -mt-10 transition-colors"></div>
        <div class="relative z-10 flex-1">
           <h3 class="text-3xl md:text-4xl font-black text-slate-800 dark:text-slate-100 tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-indigo-700 to-blue-600 dark:from-indigo-300 dark:to-cyan-300 transition-colors">Sistem & Kalibrasi</h3>
           <p class="text-slate-500 dark:text-slate-400 mt-2 md:mt-3 font-medium text-base md:text-lg transition-colors">Atur sensitivitas dan kalibrasi sensor cerdas secara presisi.</p>
        </div>
     </div>

     <!-- Loading Skeleton -->
     <div v-if="isLoading" class="grid grid-cols-1 lg:grid-cols-2 gap-8 animate-pulse">
        <div class="bg-white/50 dark:bg-white/[0.04] rounded-[2.5rem] h-96 transition-colors"></div>
        <div class="bg-white/50 dark:bg-white/[0.04] rounded-[2.5rem] h-96 transition-colors"></div>
     </div>

     <template v-else>
     <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
        <!-- LDR Setting -->
        <div class="bg-white/80 dark:bg-white/[0.04] backdrop-blur-xl p-6 md:p-10 rounded-3xl md:rounded-[2.5rem] shadow-[0_8px_30px_rgba(0,0,0,0.03)] dark:shadow-none border border-white dark:border-white/10 relative overflow-hidden group transition-all hover:shadow-[0_8px_40px_rgba(59,130,246,0.15)] flex flex-col h-full duration-500">
           <div class="absolute top-0 right-0 w-48 h-48 bg-yellow-200 dark:bg-amber-500/20 opacity-20 rounded-full -mr-16 -mt-16 blur-2xl transition-transform duration-700 group-hover:scale-125"></div>
           
           <div class="flex flex-row items-center gap-4 sm:gap-6 mb-6 sm:mb-8 relative z-10">
              <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-yellow-100 to-amber-100 dark:from-amber-500/20 dark:to-orange-500/20 rounded-xl sm:rounded-[1.25rem] flex items-center justify-center text-2xl sm:text-4xl shadow-sm dark:shadow-none border border-yellow-200 dark:border-amber-500/30 rotate-3 group-hover:rotate-6 transition-all flex-shrink-0">☀️</div>
              <div>
                 <h4 class="font-extrabold text-xl sm:text-2xl text-slate-800 dark:text-slate-100 tracking-tight transition-colors">Fotosensor Cahaya</h4>
                 <div class="inline-flex items-center gap-2 mt-1 px-2.5 sm:px-3 py-1 bg-amber-50 dark:bg-amber-500/10 rounded-md sm:rounded-lg border border-amber-100 dark:border-amber-500/20 transition-colors">
                    <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-amber-500 animate-pulse"></div>
                    <p class="text-[9px] sm:text-[11px] font-black text-amber-600 dark:text-amber-400 uppercase tracking-widest transition-colors">Batas Gelap Maksimum</p>
                 </div>
              </div>
           </div>
           
           <div class="mb-3 sm:mb-4 flex justify-between items-center relative z-10">
              <span class="text-[10px] sm:text-sm font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider transition-colors">Trigger Rel</span>
              <span class="text-4xl sm:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500 drop-shadow-sm dark:drop-shadow-none tabular-nums">{{ local_ldr_threshold }}%</span>
           </div>

           <div class="relative py-2 sm:py-4 mb-2 z-10 flex items-center gap-3 sm:gap-4">
              <button @click="adjustLdr(-1)" class="w-8 h-8 sm:w-12 sm:h-12 rounded-full bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-500/20 flex items-center justify-center font-bold text-lg sm:text-2xl flex-shrink-0 transition-colors border border-amber-200 dark:border-amber-500/30 active:scale-90 shadow-sm">-</button>
              
              <div class="relative w-full h-4 sm:h-6 flex items-center">
                 <div class="absolute w-full h-2.5 sm:h-4 bg-slate-100 dark:bg-slate-800/50 rounded-full shadow-inner border border-slate-200 dark:border-white/5"></div>
                 <div class="absolute h-2.5 sm:h-4 rounded-full bg-gradient-to-r from-yellow-300 to-orange-500 shadow-[0_0_15px_rgba(245,158,11,0.5)] transition-all ease-out pointer-events-none" :style="`width: ${local_ldr_threshold}%`"></div>
                 <input type="range" min="0" max="100" v-model.number="local_ldr_threshold" 
                        class="w-full absolute h-2.5 sm:h-4 appearance-none bg-transparent cursor-grab active:cursor-grabbing outline-none z-20 
                               [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-6 [&::-webkit-slider-thumb]:h-6 sm:[&::-webkit-slider-thumb]:w-8 sm:[&::-webkit-slider-thumb]:h-8 [&::-webkit-slider-thumb]:bg-white dark:[&::-webkit-slider-thumb]:bg-slate-800 [&::-webkit-slider-thumb]:border-4 [&::-webkit-slider-thumb]:border-orange-500 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:shadow-md [&::-webkit-slider-thumb]:transition-transform [&::-webkit-slider-thumb]:hover:scale-125">
              </div>

              <button @click="adjustLdr(1)" class="w-8 h-8 sm:w-12 sm:h-12 rounded-full bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-500/20 flex items-center justify-center font-bold text-lg sm:text-2xl flex-shrink-0 transition-colors border border-amber-200 dark:border-amber-500/30 active:scale-90 shadow-sm">+</button>
           </div>

           <!-- Confirmation Box LDR -->
           <transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0 -translate-y-4 max-h-0" enter-to-class="opacity-100 translate-y-0 max-h-40" leave-active-class="transition-all duration-200 ease-in" leave-from-class="opacity-100 max-h-40" leave-to-class="opacity-0 max-h-0">
             <div v-if="local_ldr_threshold !== settings.ldr_threshold" class="mb-4 bg-amber-50/80 dark:bg-amber-900/20 p-4 rounded-2xl border border-amber-200 dark:border-amber-500/30 shadow-lg shadow-amber-500/5 flex flex-col gap-3 relative z-20">
                <div class="flex justify-between items-center">
                   <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Konfirmasi Perubahan Cahaya</span>
                   <span class="text-xs font-black px-2 py-1 rounded-md" :class="(local_ldr_threshold - settings.ldr_threshold) > 0 ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400' : 'bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400'">
                      Selisih: {{(local_ldr_threshold - settings.ldr_threshold) > 0 ? '+' : ''}}{{local_ldr_threshold - settings.ldr_threshold}}%
                   </span>
                </div>
                <div class="flex gap-2">
                   <button @click="confirmSaveLdr" class="flex-1 bg-gradient-to-r from-amber-500 to-orange-500 text-white py-2.5 rounded-xl font-bold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all active:scale-95 text-sm">SIMPAN</button>
                   <button @click="cancelSaveLdr" class="flex-1 bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-300 py-2.5 rounded-xl font-bold hover:bg-slate-50 dark:hover:bg-slate-600 border border-slate-200 dark:border-slate-600 transition-all active:scale-95 text-sm">BATAL</button>
                </div>
             </div>
           </transition>
           
           <div class="mt-auto pt-6 bg-gradient-to-br from-amber-50 to-orange-50/50 dark:from-amber-900/10 dark:to-orange-900/10 p-6 rounded-2xl border border-amber-100/50 dark:border-amber-500/10 text-amber-900 dark:text-amber-200/80 leading-relaxed shadow-inner dark:shadow-none transition-colors">
             <strong class="text-amber-700 dark:text-amber-400 bg-amber-100 dark:bg-amber-500/20 px-2 py-1 rounded text-xs uppercase tracking-widest block mb-2 w-max transition-colors">Cara Kerja AI</strong>
             Jika kadar terik matahari jatuh <span class="font-extrabold bg-amber-200/50 dark:bg-amber-500/30 px-1 rounded">di bawah {{ settings.ldr_threshold }}%</span> (Mendung/Malam), jemuran otomatis akan <strong class="dark:text-amber-200">ditarik masuk</strong>.
           </div>
        </div>

        <!-- Rain Setting -->
        <div class="bg-white/80 dark:bg-white/[0.04] backdrop-blur-xl p-6 md:p-10 rounded-3xl md:rounded-[2.5rem] shadow-[0_8px_30px_rgba(0,0,0,0.03)] dark:shadow-none border border-white dark:border-white/10 relative overflow-hidden group transition-all hover:shadow-[0_8px_40px_rgba(99,102,241,0.15)] flex flex-col h-full duration-500">
           <div class="absolute top-0 right-0 w-48 h-48 bg-indigo-200 dark:bg-indigo-500/20 opacity-20 rounded-full -mr-16 -mt-16 blur-2xl transition-transform duration-700 group-hover:scale-125"></div>
           
           <div class="flex flex-row items-center gap-4 sm:gap-6 mb-6 sm:mb-8 relative z-10">
              <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-indigo-100 to-blue-100 dark:from-indigo-500/20 dark:to-blue-500/20 rounded-xl sm:rounded-[1.25rem] flex items-center justify-center text-2xl sm:text-4xl shadow-sm dark:shadow-none border border-indigo-200 dark:border-indigo-500/30 -rotate-3 group-hover:-rotate-6 transition-all flex-shrink-0">🌧️</div>
              <div>
                 <h4 class="font-extrabold text-xl sm:text-2xl text-slate-800 dark:text-slate-100 tracking-tight transition-colors">Sensor Presipitasi</h4>
                 <div class="inline-flex items-center gap-2 mt-1 px-2.5 sm:px-3 py-1 bg-indigo-50 dark:bg-indigo-500/10 rounded-md sm:rounded-lg border border-indigo-100 dark:border-indigo-500/20 transition-colors">
                    <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-indigo-500 animate-pulse"></div>
                    <p class="text-[9px] sm:text-[11px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest transition-colors">Toleransi Puncak Air</p>
                 </div>
              </div>
           </div>
           
           <div class="mb-3 sm:mb-4 flex justify-between items-center relative z-10">
              <span class="text-[10px] sm:text-sm font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider transition-colors">Trigger Rel</span>
              <span class="text-4xl sm:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-indigo-600 dark:from-indigo-400 dark:to-cyan-400 drop-shadow-sm dark:drop-shadow-none tabular-nums">{{ local_rain_threshold }}%</span>
           </div>

           <div class="relative py-2 sm:py-4 mb-2 z-10 flex items-center gap-3 sm:gap-4">
              <button @click="adjustRain(-1)" class="w-8 h-8 sm:w-12 sm:h-12 rounded-full bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 flex items-center justify-center font-bold text-lg sm:text-2xl flex-shrink-0 transition-colors border border-indigo-200 dark:border-indigo-500/30 active:scale-90 shadow-sm">-</button>
              
              <div class="relative w-full h-4 sm:h-6 flex items-center">
                 <div class="absolute w-full h-2.5 sm:h-4 bg-slate-100 dark:bg-slate-800/50 rounded-full shadow-inner border border-slate-200 dark:border-white/5"></div>
                 <div class="absolute h-2.5 sm:h-4 rounded-full bg-gradient-to-r from-blue-400 to-indigo-600 shadow-[0_0_15px_rgba(99,102,241,0.5)] transition-all ease-out pointer-events-none" :style="`width: ${local_rain_threshold}%`"></div>
                 <input type="range" min="0" max="100" v-model.number="local_rain_threshold" 
                        class="w-full absolute h-2.5 sm:h-4 appearance-none bg-transparent cursor-grab active:cursor-grabbing outline-none z-20 
                               [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-6 [&::-webkit-slider-thumb]:h-6 sm:[&::-webkit-slider-thumb]:w-8 sm:[&::-webkit-slider-thumb]:h-8 [&::-webkit-slider-thumb]:bg-white dark:[&::-webkit-slider-thumb]:bg-slate-800 [&::-webkit-slider-thumb]:border-4 [&::-webkit-slider-thumb]:border-indigo-500 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:shadow-md [&::-webkit-slider-thumb]:transition-transform [&::-webkit-slider-thumb]:hover:scale-125">
              </div>

              <button @click="adjustRain(1)" class="w-8 h-8 sm:w-12 sm:h-12 rounded-full bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 flex items-center justify-center font-bold text-lg sm:text-2xl flex-shrink-0 transition-colors border border-indigo-200 dark:border-indigo-500/30 active:scale-90 shadow-sm">+</button>
           </div>

           <!-- Confirmation Box Rain -->
           <transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0 -translate-y-4 max-h-0" enter-to-class="opacity-100 translate-y-0 max-h-40" leave-active-class="transition-all duration-200 ease-in" leave-from-class="opacity-100 max-h-40" leave-to-class="opacity-0 max-h-0">
             <div v-if="local_rain_threshold !== settings.rain_threshold" class="mb-4 bg-indigo-50/80 dark:bg-indigo-900/20 p-4 rounded-2xl border border-indigo-200 dark:border-indigo-500/30 shadow-lg shadow-indigo-500/5 flex flex-col gap-3 relative z-20">
                <div class="flex justify-between items-center">
                   <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Konfirmasi Perubahan Presipitasi</span>
                   <span class="text-xs font-black px-2 py-1 rounded-md" :class="(local_rain_threshold - settings.rain_threshold) > 0 ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400' : 'bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400'">
                      Selisih: {{(local_rain_threshold - settings.rain_threshold) > 0 ? '+' : ''}}{{local_rain_threshold - settings.rain_threshold}}%
                   </span>
                </div>
                <div class="flex gap-2">
                   <button @click="confirmSaveRain" class="flex-1 bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-2.5 rounded-xl font-bold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all active:scale-95 text-sm">SIMPAN</button>
                   <button @click="cancelSaveRain" class="flex-1 bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-300 py-2.5 rounded-xl font-bold hover:bg-slate-50 dark:hover:bg-slate-600 border border-slate-200 dark:border-slate-600 transition-all active:scale-95 text-sm">BATAL</button>
                </div>
             </div>
           </transition>
           
           <div class="mt-auto pt-6 bg-gradient-to-br from-indigo-50 to-blue-50/50 dark:from-indigo-900/10 dark:to-blue-900/10 p-6 rounded-2xl border border-indigo-100/50 dark:border-indigo-500/10 text-indigo-900 dark:text-indigo-200/80 leading-relaxed shadow-inner dark:shadow-none transition-colors">
             <strong class="text-indigo-700 dark:text-indigo-400 bg-indigo-100 dark:bg-indigo-500/20 px-2 py-1 rounded text-xs uppercase tracking-widest block mb-2 w-max transition-colors">Cara Kerja AI</strong>
             Jika kadar air di atas modul melebihi batas <span class="font-extrabold bg-indigo-200/50 dark:bg-indigo-500/30 px-1 rounded">{{ settings.rain_threshold }}%</span>, sistem menganggap hujan turun dan <strong class="dark:text-indigo-200">mengamankan jemuran</strong>.
           </div>
        </div>
     </div>

     <!-- Glowing Master Switch Card -->
     <div class="rounded-3xl md:rounded-[2.5rem] shadow-2xl relative overflow-hidden group transition-all duration-500"
          :class="settings.is_auto_mode ? 'bg-gradient-to-br from-slate-900 via-[#1e293b] to-black text-white' : 'bg-gradient-to-br from-rose-600 via-red-600 to-rose-800 text-white'">
        
        <div v-show="settings.is_auto_mode" class="absolute left-1/4 top-1/2 -translate-y-1/2 w-96 h-96 bg-emerald-500/20 rounded-full blur-[80px] animate-[pulse_4s_ease-in-out_infinite]"></div>
        <div v-show="!settings.is_auto_mode" class="absolute right-1/4 top-1/2 -translate-y-1/2 w-96 h-96 bg-yellow-500/30 rounded-full blur-[80px] animate-[pulse_2s_ease-in-out_infinite]"></div>

        <div class="p-6 md:p-12 flex flex-col md:flex-row justify-between items-center relative z-10 gap-6 md:gap-8">
           <div class="max-w-xl text-center md:text-left">
              <div class="inline-flex items-center gap-2 mb-4 px-4 py-1.5 rounded-full border bg-white/10 backdrop-blur-md"
                   :class="settings.is_auto_mode ? 'border-emerald-500/30 text-emerald-300' : 'border-yellow-300/30 text-yellow-200'">
                  <div class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full animate-ping" :class="settings.is_auto_mode ? 'bg-emerald-400' : 'bg-yellow-400'"></div>
                  <span class="text-[10px] sm:text-xs font-black uppercase tracking-[0.2em]">{{ settings.is_auto_mode ? 'Sistem Aktif' : 'Perlu Intervensi' }}</span>
              </div>
              <h4 class="font-black text-3xl md:text-4xl lg:text-5xl mb-3 sm:mb-4 tracking-tight drop-shadow-md">
                 {{ settings.is_auto_mode ? 'Sistem Otomatis Aktif' : 'Sistem Manual Aktif!' }}
              </h4>
              <p class="text-white/70 text-sm sm:text-base md:text-lg font-medium leading-relaxed">
                 {{ settings.is_auto_mode ? 'ESP32 mengontrol jemuran berdasarkan sensor real-time. Anda bisa bersantai.' : 'Mode manual aktif. Gunakan tombol kontrol dari dashboard untuk mengontrol jemuran.' }}
              </p>
           </div>
           
           <button @click="toggleAutoMode" 
                   class="w-full md:w-auto flex-shrink-0 relative group/btn px-6 sm:px-10 py-3.5 sm:py-5 rounded-2xl sm:rounded-[2rem] font-black tracking-widest uppercase transition-all duration-500 overflow-hidden text-sm sm:text-xl z-20 border-2 mt-2 md:mt-0"
                   :class="settings.is_auto_mode ? 'bg-emerald-500 border-emerald-400 text-white shadow-[0_0_30px_rgba(16,185,129,0.3)] sm:shadow-[0_0_50px_rgba(16,185,129,0.5)] hover:bg-emerald-400 hover:scale-105 active:scale-95' : 'bg-white text-rose-600 border-white shadow-[0_0_30px_rgba(255,255,255,0.2)] sm:shadow-[0_0_50px_rgba(255,255,255,0.3)] hover:bg-slate-50 hover:scale-105 active:scale-95'">
               <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-[150%] skew-x-[-20deg] group-hover/btn:translate-x-[150%] transition-transform duration-700"></div>
               <div class="relative flex flex-row items-center justify-center gap-2 sm:gap-3 text-center">
                  <span class="text-xl sm:text-3xl">{{ settings.is_auto_mode ? '⚡' : '🛡️' }}</span>
                  <span>{{ settings.is_auto_mode ? 'GABUNG MANUAL' : 'REAKTIVASI OTOMATIS' }}</span>
               </div>
           </button>
        </div>
     </div>

     <!-- Danger Zone: Reset WiFi -->
     <div class="bg-rose-50/50 dark:bg-rose-950/20 backdrop-blur-xl p-6 md:p-8 rounded-3xl md:rounded-[2rem] border border-rose-200 dark:border-rose-900/50 flex flex-col md:flex-row justify-between items-center gap-6 md:gap-8 relative overflow-hidden transition-all duration-500 mt-8 md:mt-12">
        <div class="absolute right-0 bottom-0 w-64 h-64 bg-rose-500/10 rounded-full blur-[80px] pointer-events-none transition-colors"></div>
        <div class="relative z-10 flex flex-row items-center sm:items-center text-left gap-4 sm:gap-6 md:w-2/3">
           <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-rose-500 to-red-600 rounded-xl sm:rounded-2xl flex items-center justify-center text-white text-xl sm:text-3xl shadow-lg shadow-rose-500/30 flex-shrink-0">
              <svg class="w-6 h-6 sm:w-8 sm:h-8 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
           </div>
           <div>
              <h4 class="font-black text-base sm:text-xl text-rose-700 dark:text-rose-400 tracking-tight leading-tight mb-0.5 sm:mb-1">Zona Bahaya: Hapus Penyandingan</h4>
              <p class="text-[10px] sm:text-sm font-medium text-rose-600/80 dark:text-rose-400/80 leading-snug">
                 Tindakan ini akan menghapus kredensial WiFi. Perangkat akan me-restart ke Hotspot Setup.
              </p>
           </div>
        </div>
        <div class="relative z-10 w-full md:w-auto">
           <button v-if="!confirmReset" @click="confirmReset = true" class="w-full md:w-auto bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-400 px-6 py-3.5 sm:py-4 rounded-2xl sm:rounded-[1.25rem] font-bold text-sm sm:text-base tracking-wide hover:bg-rose-200 dark:hover:bg-rose-500/30 transition-all shadow-sm border border-rose-200 dark:border-rose-500/30 active:scale-95 whitespace-nowrap">
              HAPUS KONEKSI WIFI
           </button>
           <div v-else class="flex flex-col gap-2 w-full md:w-auto transition-all">
              <span class="text-xs font-bold text-rose-600 dark:text-rose-400 text-center uppercase tracking-widest mb-1">Apakah Anda Yakin?</span>
              <div class="flex gap-2">
                 <button @click="executeResetWiFi" class="flex-1 md:flex-none bg-rose-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-rose-700 shadow-lg shadow-rose-600/30 active:scale-95 transition-all whitespace-nowrap">
                    YA, HAPUS!
                 </button>
                 <button @click="confirmReset = false" class="flex-1 md:flex-none bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 px-6 py-3 rounded-xl font-bold hover:bg-slate-300 dark:hover:bg-slate-600 active:scale-95 transition-all whitespace-nowrap">
                    BATAL
                 </button>
              </div>
           </div>
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
      confirmReset: false,
      local_ldr_threshold: null,
      local_rain_threshold: null,
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
        if(response.data) {
           this.settings = response.data.setting;
           
           // Initialize local variables only if they haven't been edited
           if (this.local_ldr_threshold === null || this.local_ldr_threshold === this.settings.ldr_threshold) {
              this.local_ldr_threshold = this.settings.ldr_threshold;
           }
           if (this.local_rain_threshold === null || this.local_rain_threshold === this.settings.rain_threshold) {
              this.local_rain_threshold = this.settings.rain_threshold;
           }
        }
      } catch (error) { console.error(error); }
      finally { this.isLoading = false; }
    },
    async saveSettings() {
      try {
        const payload = {
          is_auto_mode: this.settings.is_auto_mode,
          ldr_threshold: this.settings.ldr_threshold,
          rain_threshold: this.settings.rain_threshold,
          manual_position: this.settings.manual_position,
          owner_name: this.settings.owner_name,
        };
        await axios.post('/api/update-setting', payload);
        this.$emit('toast', { type: 'success', title: 'Tersimpan!', message: 'Pengaturan berhasil diperbarui.' });
      } catch (error) { 
        this.$emit('toast', { type: 'error', title: 'Gagal!', message: 'Tidak bisa menyimpan pengaturan.' });
      }
    },
    async confirmSaveLdr() {
       this.settings.ldr_threshold = this.local_ldr_threshold;
       await this.saveSettings();
    },
    cancelSaveLdr() {
       this.local_ldr_threshold = this.settings.ldr_threshold;
    },
    async confirmSaveRain() {
       this.settings.rain_threshold = this.local_rain_threshold;
       await this.saveSettings();
    },
    cancelSaveRain() {
       this.local_rain_threshold = this.settings.rain_threshold;
    },
    adjustLdr(amount) {
       let val = this.local_ldr_threshold + amount;
       if (val < 0) val = 0;
       if (val > 100) val = 100;
       this.local_ldr_threshold = val;
    },
    adjustRain(amount) {
       let val = this.local_rain_threshold + amount;
       if (val < 0) val = 0;
       if (val > 100) val = 100;
       this.local_rain_threshold = val;
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
    },
    async executeResetWiFi() {
      try {
        this.confirmReset = false;
        await axios.post('/api/push-command', { command: 'reset_wifi' });
        this.$emit('toast', { type: 'success', title: 'Perintah Terkirim', message: 'Instruksi hapus WiFi telah dikirim ke perangkat. Perangkat akan segera restart.' });
      } catch (error) {
        this.$emit('toast', { type: 'error', title: 'Gagal', message: 'Tidak dapat mengirim instruksi.' });
      }
    }
  }
}
</script>
