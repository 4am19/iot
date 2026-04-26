<template>
  <div class="space-y-6 pb-10 relative transition-colors duration-500">
    
    <!-- Loading Skeleton -->
    <div v-if="isLoading" class="space-y-6 animate-pulse">
       <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
          <div class="xl:col-span-2 bg-white/5 rounded-2xl h-[300px] border border-white/5"></div>
          <div class="bg-white/5 rounded-2xl h-[300px] border border-white/5"></div>
       </div>
       <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div class="bg-white/5 rounded-xl h-32 border border-white/5" v-for="n in 4" :key="n"></div>
       </div>
       <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="bg-white/5 rounded-2xl h-[340px] border border-white/5"></div>
          <div class="bg-white/5 rounded-2xl h-[340px] border border-white/5"></div>
       </div>
    </div>

    <!-- Interface -->
    <div v-else class="space-y-6">
      
      <!-- Top Row -->
      <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
        <!-- Hero Banner Glassmorphic -->
        <div class="xl:col-span-2 relative bg-white/80 dark:bg-white/[0.04] backdrop-blur-xl rounded-2xl p-6 md:p-10 border border-white/50 dark:border-white/[0.08] overflow-hidden group animation-fade-in transition-all duration-500 hover:border-indigo-400/30 dark:hover:border-indigo-500/20 hover:shadow-[0_4px_20px_rgba(99,102,241,0.08)] dark:hover:shadow-[0_0_40px_rgba(99,102,241,0.08)] shadow-sm dark:shadow-none">
           <!-- Animated background blobs -->
           <div class="absolute -top-32 -left-32 w-80 h-80 bg-indigo-500/10 rounded-full blur-[80px] animate-blob"></div>
           <div class="absolute top-10 -right-20 w-64 h-64 bg-purple-500/10 rounded-full blur-[80px] animate-blob animation-delay-2000"></div>
           <div class="absolute -bottom-40 left-20 w-80 h-80 bg-cyan-500/8 rounded-full blur-[80px] animate-blob animation-delay-4000"></div>

           <div class="flex flex-col md:flex-row items-center gap-10 md:gap-14 relative z-10 w-full justify-between h-full">
              <div class="w-full text-center md:text-left flex flex-col justify-center">
                <div class="inline-flex items-center space-x-2 bg-indigo-50 dark:bg-white/[0.06] backdrop-blur-md px-4 py-1.5 rounded-full text-[10px] sm:text-xs font-bold tracking-[0.2em] mb-5 border border-indigo-100 dark:border-white/10 text-slate-500 dark:text-slate-400 max-w-max mx-auto md:mx-0 shadow-sm dark:shadow-none transition-colors">
                  <span class="w-2 h-2 rounded-full" :class="settings.is_auto_mode ? 'bg-emerald-500 dark:bg-emerald-400 animate-pulse' : 'bg-rose-500 dark:bg-rose-400'"></span>
                  <span>STATUS POSISI AKTUAL</span>
                </div>
                
                <h3 class="text-3xl md:text-5xl lg:text-6xl font-black mb-4 tracking-tighter relative inline-block transition-all duration-300">
                  <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 via-purple-600 to-cyan-600 dark:from-indigo-300 dark:via-purple-300 dark:to-cyan-300 drop-shadow-sm dark:drop-shadow-none">
                    {{ settings.is_auto_mode ? latestData.clothesline_status : settings.manual_position || 'Memuat...' }}
                  </span>
                </h3>
                
                <div class="flex items-center justify-center md:justify-start gap-3 mb-6">
                   <div class="px-3 py-1.5 bg-white/60 dark:bg-white/[0.06] rounded-lg text-slate-600 dark:text-slate-300 text-sm font-semibold border border-slate-200 dark:border-white/10 flex items-center gap-2 shadow-sm dark:shadow-none transition-colors">
                     <svg class="w-4 h-4 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                     {{ settings.is_auto_mode ? 'Kecerdasan Buatan (Auto)' : 'Manual Override Aktif' }}
                   </div>
                </div>
                
                <div class="flex justify-center md:justify-start">
                   <div class="bg-white/60 dark:bg-white/[0.06] backdrop-blur-xl px-5 py-3.5 rounded-xl border border-slate-200 dark:border-white/10 flex items-center gap-4 group-hover:scale-105 transition-all duration-500 shadow-sm dark:shadow-none">
                      <div class="w-11 h-11 bg-indigo-50 dark:bg-indigo-500/15 rounded-lg flex items-center justify-center text-2xl border border-indigo-100 dark:border-indigo-500/20">
                        {{ latestData.weather_condition === 'Hujan' ? '🌧️' : latestData.weather_condition === 'Cerah' ? '☀️' : '⛅' }}
                      </div>
                      <div>
                         <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.2em] mb-0.5">Prediksi Cuaca</p>
                         <p class="text-lg font-extrabold tracking-tight text-slate-800 dark:text-slate-200 transition-colors">{{ latestData.weather_condition || 'Memuat...' }}</p>
                      </div>
                   </div>
                </div>
              </div>

              <!-- 3D Inspired Weather Orb -->
              <div class="flex-shrink-0 relative group perspective-1000 mt-6 md:mt-0">
                 <div class="absolute inset-0 rounded-full blur-[50px] animate-pulse transition-colors duration-1000" 
                      :class="clotheslineStatus === 'Di Dalam' ? 'bg-slate-500/30' : 'bg-amber-500/30'"></div>
                 
                 <div class="w-40 h-40 md:w-48 md:h-48 rounded-full relative transform-style-3d transition-transform duration-1000 hover:rotate-y-12 hover:rotate-x-12 z-10 flex items-center justify-center text-7xl md:text-8xl shadow-xl dark:shadow-2xl ring-1"
                      :class="clotheslineStatus === 'Di Dalam' ? 'bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 border-2 border-white dark:border-slate-600 ring-slate-200 dark:ring-slate-600/50' : 'bg-gradient-to-br from-amber-300/90 to-orange-400/90 dark:from-amber-500/80 dark:to-orange-600/80 border-2 border-amber-200/80 dark:border-amber-400/50 ring-amber-300/50 dark:ring-amber-400/30'">
                    <span class="transform drop-shadow-lg dark:drop-shadow-2xl hover:scale-110 transition-transform duration-500">{{ clotheslineStatus === 'Di Dalam' ? '☁️' : '☀️' }}</span>
                    <div class="absolute inset-0 rounded-full bg-gradient-to-tr from-transparent via-white/40 to-white/60 dark:via-white/10 dark:to-white/20 opacity-50 pointer-events-none mix-blend-overlay z-20"></div>
                 </div>
              </div>
           </div>
        </div>

        <!-- Radial Gauges (Sensor Realtime) -->
        <div class="bg-white/80 dark:bg-white/[0.04] backdrop-blur-xl rounded-2xl p-6 border border-white/50 dark:border-white/[0.08] flex flex-col items-center justify-center relative overflow-hidden animation-fade-in delay-100 hover:border-indigo-400/30 dark:hover:border-indigo-500/20 transition-all duration-500">
          <h3 class="text-slate-600 dark:text-slate-500 font-bold text-[11px] uppercase tracking-[0.2em] mb-6 text-center relative z-10 bg-white/50 dark:bg-white/[0.06] px-4 py-1.5 rounded-full border border-slate-200 dark:border-white/10">Pembacaan Fisik</h3>
          
          <div class="flex flex-col gap-8 w-full justify-center items-center h-full pb-4">
             
             <div class="flex w-full justify-around items-center">
                <!-- Circular LDR Gauge -->
                <div class="relative flex flex-col items-center group cursor-default">
                  <svg class="w-28 h-28 md:w-32 md:h-32 transform -rotate-90 drop-shadow-md" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="40" stroke="rgba(245, 158, 11, 0.1)" stroke-width="7" fill="none"></circle>
                    <circle cx="50" cy="50" r="40" class="text-amber-400 group-hover:text-amber-300 transition-all duration-1000 ease-out" stroke="currentColor" stroke-width="7" fill="none" stroke-linecap="round" :stroke-dasharray="251.2" :stroke-dashoffset="251.2 - (251.2 * displayLdr / 100)" style="filter: drop-shadow(0 0 6px rgba(245,158,11,0.5));"></circle>
                  </svg>
                  <div class="absolute inset-0 flex flex-col items-center justify-center -mt-5">
                     <span class="text-xl mt-1 text-amber-500 dark:text-amber-400 animate-bounce-slow">☀️</span>
                     <span class="text-xl font-black text-slate-800 dark:text-slate-200 tracking-tighter relative">{{ displayLdr }}<span class="text-xs font-bold absolute top-0.5 -right-3 text-slate-500 dark:text-slate-400">%</span></span>
                  </div>
                  <span class="mt-3 text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-white/50 dark:bg-white/[0.06] px-3 py-1 rounded-lg border border-slate-200 dark:border-white/10">LDR Matahari</span>
                </div>

                <!-- Circular Rain Gauge -->
                <div class="relative flex flex-col items-center group cursor-default">
                  <svg class="w-28 h-28 md:w-32 md:h-32 transform -rotate-90 drop-shadow-md" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="40" stroke="rgba(99, 102, 241, 0.1)" stroke-width="7" fill="none"></circle>
                    <circle cx="50" cy="50" r="40" class="text-indigo-400 group-hover:text-indigo-300 transition-all duration-1000 ease-out" stroke="currentColor" stroke-width="7" fill="none" stroke-linecap="round" :stroke-dasharray="251.2" :stroke-dashoffset="251.2 - (251.2 * displayRain / 100)" style="filter: drop-shadow(0 0 6px rgba(99,102,241,0.5));"></circle>
                  </svg>
                  <div class="absolute inset-0 flex flex-col items-center justify-center -mt-5">
                     <span class="text-xl mt-1 text-indigo-500 dark:text-indigo-400 animate-bounce-slow" style="animation-delay: 1s">🌧️</span>
                     <span class="text-xl font-black text-slate-800 dark:text-slate-200 tracking-tighter relative">{{ displayRain }}<span class="text-xs font-bold absolute top-0.5 -right-3 text-slate-500 dark:text-slate-400">%</span></span>
                  </div>
                  <span class="mt-3 text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider bg-white/50 dark:bg-white/[0.06] px-3 py-1 rounded-lg border border-slate-200 dark:border-white/10">Intensitas Air</span>
                </div>
             </div>
             
          </div>
        </div>
      </div>

      <!-- Summary Stats Row -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
         <div v-for="(stat, index) in summaryStats" :key="stat.label" 
              class="bg-white/80 dark:bg-white/[0.04] backdrop-blur-xl rounded-xl p-4 md:p-5 border border-white/50 dark:border-white/[0.08] group hover:-translate-y-1 transition-all duration-500 cursor-default animation-fade-in-up hover:border-indigo-400/30 dark:hover:border-indigo-500/20 hover:shadow-[0_4px_20px_rgba(99,102,241,0.08)] dark:hover:shadow-[0_0_25px_rgba(99,102,241,0.06)] shadow-sm dark:shadow-none"
              :style="{ animationDelay: `${(index + 2) * 100}ms` }">
            <div class="flex items-center gap-3 mb-3">
               <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition-transform duration-300" :class="stat.bgClass">{{ stat.emoji }}</div>
               <span class="text-[10px] md:text-xs font-bold uppercase tracking-widest text-slate-500 leading-tight">{{ stat.label }}</span>
            </div>
            <p class="text-3xl md:text-4xl font-black tracking-tighter tabular-nums transition-colors duration-300" :class="stat.valueClass">{{ stat.value }}<span class="text-sm font-bold ml-1 opacity-60">{{ stat.unit }}</span></p>
         </div>
      </div>

      <!-- Chart + Control Row -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
         
         <!-- Neumorphic Main Switch -->
         <div class="bg-white/80 dark:bg-white/[0.04] backdrop-blur-xl rounded-2xl p-6 md:p-8 border border-white/50 dark:border-white/[0.08] flex flex-col items-center justify-center min-h-[300px] relative overflow-hidden animation-fade-in-up delay-600 group hover:border-indigo-400/30 dark:hover:border-indigo-500/20 transition-all duration-500 shadow-sm dark:shadow-none">
             <!-- Interactive Glow Background -->
             <div class="absolute inset-0 transition-opacity duration-700 opacity-10 pointer-events-none"
                  :class="settings.is_auto_mode ? 'bg-gradient-to-tr from-emerald-500 to-emerald-900' : 'bg-gradient-to-tr from-rose-500 to-rose-900'"></div>
             
             <div class="inline-flex items-center space-x-2 bg-indigo-50 dark:bg-white/[0.06] backdrop-blur-md px-4 py-1.5 rounded-full text-[10px] font-bold tracking-[0.2em] mb-5 border border-indigo-100 dark:border-white/10 relative z-10 text-slate-500 dark:text-slate-400 transition-colors">
                SISTEM PUSAT
             </div>
             
             <p class="text-center text-sm text-slate-500 mb-8 max-w-sm font-medium relative z-10">Alihkan ke Mode Manual untuk mengambil kendali rel motor secara penuh dari Kecerdasan Buatan.</p>
             
             <div class="flex flex-col items-center justify-center relative z-10 w-full">
               <button @click="toggleAutoMode" 
                       class="relative flex h-20 w-44 items-center rounded-full transition-all duration-500 focus:outline-none cursor-pointer border-[6px] overflow-hidden hover:scale-105 active:scale-95"
                       :class="settings.is_auto_mode ? 'bg-emerald-500 border-emerald-400/30 shadow-[inset_0_4px_10px_rgba(0,0,0,0.2),0_0_30px_rgba(16,185,129,0.3)]' : 'bg-slate-300 dark:bg-slate-600 border-slate-200 dark:border-slate-500/30 shadow-[inset_0_4px_10px_rgba(0,0,0,0.1)] dark:shadow-[inset_0_4px_10px_rgba(0,0,0,0.3)]'">
                 
                 <div class="absolute inset-0 bg-black/5 opacity-0 hover:opacity-100 transition-opacity pointer-events-none"></div>

                 <!-- Toggle Knob -->
                 <span class="inline-flex h-14 w-14 transform rounded-full bg-white shadow-[0_4px_12px_rgba(0,0,0,0.3)] transition-transform duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)] items-center justify-center relative z-10"
                       :class="settings.is_auto_mode ? 'translate-x-[6.5rem]' : 'translate-x-1.5'">
                    <i v-if="settings.is_auto_mode" class="text-emerald-500 text-2xl font-black filter drop-shadow-sm flex items-center h-full not-italic">🤖</i>
                    <i v-else class="text-slate-400 text-2xl font-black filter drop-shadow-sm flex items-center h-full not-italic">✋</i>
                 </span>
                 
                 <!-- Status Text inside Toggle -->
                 <span class="absolute right-6 font-black text-white/90 text-sm tracking-widest transition-opacity duration-300" :class="settings.is_auto_mode ? 'opacity-100' : 'opacity-0'">AUTO</span>
                 <span class="absolute left-6 font-black text-slate-400 text-sm tracking-widest transition-opacity duration-300" :class="!settings.is_auto_mode ? 'opacity-100' : 'opacity-0'">MANUAL</span>
               </button>
               
               <div class="mt-6 px-5 py-2 rounded-xl bg-white/[0.06] border border-white/10 backdrop-blur transition-colors duration-500"
                    :class="settings.is_auto_mode ? 'text-emerald-400' : 'text-slate-300'">
                 <span class="font-bold text-sm md:text-base tracking-tight">
                    {{ settings.is_auto_mode ? 'KENDALI KECERDASAN BUATAN' : 'PENGENDALIAN MANUAL AKTIF'}}
                 </span>
               </div>
             </div>
         </div>

         <!-- Manual Control Buttons -->
         <div class="bg-white/80 dark:bg-white/[0.04] backdrop-blur-xl rounded-2xl p-6 md:p-8 border border-white/50 dark:border-white/[0.08] relative flex flex-col justify-center min-h-[300px] animation-fade-in-up delay-700 hover:border-indigo-400/30 dark:hover:border-indigo-500/20 transition-all duration-500 shadow-sm dark:shadow-none">
             <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center gap-3 mb-6">
                <div class="flex items-center gap-3">
                 <h3 class="text-slate-500 font-bold text-[11px] uppercase tracking-[0.2em] bg-white/[0.06] px-4 py-1.5 rounded-full border border-white/10">Eksekusi Manual</h3>
                 <button @click="isBleConnected ? disconnectBluetooth() : connectBluetooth()" class="px-3 py-1.5 rounded-full text-[10px] font-bold border transition-colors flex items-center gap-2 group/ble" :class="isBleConnected ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500/30 hover:bg-rose-500/15 hover:text-rose-400 hover:border-rose-500/30' : 'bg-white/5 text-slate-400 border-white/10 hover:bg-indigo-500/15 hover:text-indigo-400 hover:border-indigo-500/30'">
                   <!-- Icon Bluetooth -->
                   <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m0-16l5 5-5 5m0-10L7 9l5 5m0 0l5 5-5 5"></path></svg>
                   <span class="group-hover/ble:hidden">{{ isBleConnected ? 'BLE Terhubung' : 'Koneksi BLE' }}</span>
                   <span class="hidden group-hover/ble:inline">{{ isBleConnected ? 'Putuskan BLE' : 'Hubungkan BLE' }}</span>
                 </button>
               </div>
               <span class="relative flex h-3 w-3 self-end sm:self-auto" v-if="!settings.is_auto_mode || isBleConnected">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" :class="isBleConnected ? 'bg-indigo-400' : 'bg-rose-400'"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3" :class="isBleConnected ? 'bg-indigo-500' : 'bg-rose-500'"></span>
               </span>
             </div>

             <!-- Pending Command Indicator -->
             <transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
               <div v-if="pendingCommand" class="mb-4 flex items-center gap-3 bg-amber-500/10 border border-amber-500/20 text-amber-300 px-4 py-3 rounded-xl text-xs font-bold">
                 <svg class="w-4 h-4 animate-spin text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                 <span>Mengirim perintah ke ESP32... Tunggu response berikutnya (~10 detik)</span>
               </div>
             </transition>
             
             <!-- Glow Effect container when manual mode -->
             <div class="grid grid-cols-1 gap-5 relative z-10">
                <div v-if="!settings.is_auto_mode" class="absolute -inset-4 bg-rose-400/20 blur-2xl rounded-[3rem] animate-pulse pointer-events-none -z-10"></div>
                
                <button @click="pushCommand('move_out')" :disabled="(settings.is_auto_mode && !isBleConnected) || isSendingCommand"
                        class="relative overflow-hidden w-full py-5 md:py-6 bg-gradient-to-r from-blue-500 to-indigo-500 dark:from-blue-600 dark:to-indigo-600 hover:from-blue-400 hover:to-indigo-400 dark:hover:from-blue-500 dark:hover:to-indigo-500 text-white disabled:from-slate-200 disabled:to-slate-200 dark:disabled:from-slate-700 dark:disabled:to-slate-700 disabled:text-slate-400 dark:disabled:text-slate-500 font-bold text-base md:text-lg rounded-xl shadow-[0_8px_20px_rgba(79,70,229,0.25)] hover:shadow-[0_12px_30px_rgba(79,70,229,0.35)] disabled:shadow-none transition-all duration-300 outline-none transform active:scale-[0.98] disabled:active:scale-100 flex justify-center items-center gap-3 group">
                   <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 -translate-x-[150%] skew-x-[-20deg] group-hover:animate-shine disabled:hidden"></div>
                   <div class="w-9 h-9 bg-white/15 rounded-lg group-hover:scale-110 transition-transform flex items-center justify-center backdrop-blur-sm border border-white/10">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                   </div>
                   <span class="tracking-wide text-sm md:text-xl drop-shadow-md">KELUARKAN JEMURAN</span>
                </button>
                
                <button @click="pushCommand('move_in')" :disabled="(settings.is_auto_mode && !isBleConnected) || isSendingCommand"
                        class="relative overflow-hidden w-full py-5 md:py-6 bg-gradient-to-r from-rose-500 to-red-500 dark:from-rose-600 dark:to-red-600 hover:from-rose-400 hover:to-red-400 dark:hover:from-rose-500 dark:hover:to-red-500 text-white disabled:from-slate-200 disabled:to-slate-200 dark:disabled:from-slate-700 dark:disabled:to-slate-700 disabled:text-slate-400 dark:disabled:text-slate-500 font-bold text-base md:text-lg rounded-xl shadow-[0_8px_20px_rgba(244,63,94,0.25)] hover:shadow-[0_12px_30px_rgba(244,63,94,0.35)] disabled:shadow-none transition-all duration-300 outline-none transform active:scale-[0.98] disabled:active:scale-100 flex justify-center items-center gap-3 group">
                   <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 -translate-x-[150%] skew-x-[-20deg] group-hover:animate-shine disabled:hidden"></div>
                   <div class="w-9 h-9 bg-white/15 rounded-lg group-hover:scale-110 transition-transform flex items-center justify-center backdrop-blur-sm border border-white/10">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                   </div>
                   <span class="tracking-wide text-sm md:text-xl drop-shadow-md">TARIK MASUK JEMURAN</span>
                </button>
             </div>
         </div>
      </div>

      <!-- Live Chart Full Width Row -->
      <div class="bg-white/80 dark:bg-white/[0.04] backdrop-blur-xl rounded-2xl p-6 md:p-8 border border-white/50 dark:border-white/[0.08] relative overflow-hidden animation-fade-in-up delay-800 group hover:border-indigo-400/30 dark:hover:border-indigo-500/20 transition-all duration-500 shadow-sm dark:shadow-none">
          <div class="flex justify-between items-center mb-6">
             <h3 class="text-slate-600 dark:text-slate-500 font-bold text-[10px] md:text-xs uppercase tracking-[0.2em] flex items-center gap-3 transition-colors">
               <span class="p-2 bg-indigo-50 dark:bg-indigo-500/15 text-indigo-500 dark:text-indigo-400 rounded-lg border border-indigo-100 dark:border-indigo-500/20"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg></span>
               Grafik Tren Sensor
             </h3>
             <span class="text-[9px] md:text-[10px] bg-white/60 dark:bg-white/[0.06] border border-slate-200 dark:border-white/10 text-slate-500 dark:text-slate-400 px-3 py-1.5 rounded-full font-bold flex items-center gap-2 shadow-sm dark:shadow-none transition-colors">
               <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 dark:bg-indigo-400 animate-pulse"></span>
               REKAM JEJAK
             </span>
          </div>
          <div class="relative w-full rounded-xl bg-white/50 dark:bg-white/[0.03] backdrop-blur-sm p-4 border border-slate-200 dark:border-white/5" style="height: 300px;">
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
  props: {
    isDarkMode: {
      type: Boolean,
      default: true
    }
  },
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
      // Bluetooth State
      bleDevice: null,
      bleCharacteristic: null,
      isBleConnected: false,
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
          { label: 'Rata-rata LDR', value: '—', unit: '', emoji: '☀️', bgClass: 'bg-amber-500/15 border border-amber-500/20', valueClass: 'text-amber-400' },
          { label: 'Rata-rata Hujan', value: '—', unit: '', emoji: '🌧️', bgClass: 'bg-indigo-500/15 border border-indigo-500/20', valueClass: 'text-indigo-400' },
          { label: 'Total Pergerakan', value: '0', unit: 'kali', emoji: '⚙️', bgClass: 'bg-slate-500/15 border border-slate-500/20', valueClass: 'text-slate-300' },
          { label: 'Evakuasi Darurat', value: '0', unit: 'kali', emoji: '🚨', bgClass: 'bg-rose-500/15 border border-rose-500/20', valueClass: 'text-rose-400' },
        ];
      }
      const avgLdr = Math.round(logs.reduce((a, l) => a + l.ldr_value, 0) / logs.length);
      const avgRain = Math.round(logs.reduce((a, l) => a + l.rain_percentage, 0) / logs.length);
      const movements = logs.filter((l, i) => i > 0 && l.clothesline_status !== logs[i-1].clothesline_status).length;
      const emergencies = logs.filter(l => l.weather_condition.toLowerCase().includes('hujan')).length;
      return [
        { label: 'Rata-rata LDR', value: avgLdr, unit: '%', emoji: '☀️', bgClass: 'bg-amber-500/15 border border-amber-500/20', valueClass: 'text-amber-400' },
        { label: 'Rata-rata Hujan', value: avgRain, unit: '%', emoji: '🌧️', bgClass: 'bg-indigo-500/15 border border-indigo-500/20', valueClass: 'text-indigo-400' },
        { label: 'Total Pergerakan', value: movements, unit: 'kali', emoji: '⚙️', bgClass: 'bg-slate-500/15 border border-slate-500/20', valueClass: 'text-slate-300' },
        { label: 'Evakuasi Darurat', value: emergencies, unit: 'kali', emoji: '🚨', bgClass: 'bg-rose-500/15 border border-rose-500/20', valueClass: 'text-rose-400' },
      ];
    }
  },
  watch: {
    historyData: {
      deep: true,
      handler(newVal) {
        if (!this.isLoading && this.chart) {
          this.updateChart();
        }
      }
    },
    isDarkMode() {
      if (this.chart) {
        this.chart.destroy();
        this.updateChart();
      }
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
        this.chart.destroy();
      }

      const ctx = canvas.getContext('2d');
      const ldrGradient = ctx.createLinearGradient(0, 0, 0, 280);
      ldrGradient.addColorStop(0, this.isDarkMode ? 'rgba(251, 191, 36, 0.3)' : 'rgba(251, 191, 36, 0.2)');
      ldrGradient.addColorStop(1, 'rgba(251, 191, 36, 0)');
      
      const rainGradient = ctx.createLinearGradient(0, 0, 0, 280);
      rainGradient.addColorStop(0, this.isDarkMode ? 'rgba(129, 140, 248, 0.3)' : 'rgba(129, 140, 248, 0.2)');
      rainGradient.addColorStop(1, 'rgba(129, 140, 248, 0)');

      const textColor = this.isDarkMode ? '#94a3b8' : '#64748b';
      const gridColor = this.isDarkMode ? 'rgba(100,116,139,0.1)' : 'rgba(100,116,139,0.15)';
      const tooltipBg = this.isDarkMode ? 'rgba(15, 23, 42, 0.95)' : 'rgba(255, 255, 255, 0.95)';
      const tooltipTitle = this.isDarkMode ? '#e2e8f0' : '#0f172a';
      const tooltipBody = this.isDarkMode ? '#94a3b8' : '#475569';
      const tooltipBorder = this.isDarkMode ? 'rgba(100, 116, 139, 0.3)' : 'rgba(226, 232, 240, 1)';
      const pointBg = this.isDarkMode ? '#0f172a' : '#ffffff';

      this.chart = new Chart(ctx, {
        type: 'line',
        data: {
          labels,
          datasets: [
            {
              label: 'LDR',
              data: ldrData,
              borderColor: '#fbbf24',
              backgroundColor: ldrGradient,
              borderWidth: 2.5,
              fill: true,
              cubicInterpolationMode: 'monotone',
              tension: 0.4,
              pointBackgroundColor: pointBg,
              pointBorderColor: '#fbbf24',
              pointBorderWidth: 2,
              pointRadius: 3,
              pointHoverRadius: 6,
            },
            {
              label: 'Hujan',
              data: rainData,
              borderColor: '#818cf8',
              backgroundColor: rainGradient,
              borderWidth: 2.5,
              fill: true,
              cubicInterpolationMode: 'monotone',
              tension: 0.4,
              pointBackgroundColor: pointBg,
              pointBorderColor: '#818cf8',
              pointBorderWidth: 2,
              pointRadius: 3,
              pointHoverRadius: 6,
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
              labels: { usePointStyle: true, padding: 20, font: { weight: 'bold', size: 11 }, color: textColor }
            },
            tooltip: {
              backgroundColor: tooltipBg,
              titleColor: tooltipTitle,
              bodyColor: tooltipBody,
              borderColor: tooltipBorder,
              borderWidth: 1,
              titleFont: { weight: 'bold', size: 12 },
              bodyFont: { weight: 'bold', size: 12 },
              cornerRadius: 12,
              padding: 14,
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
              ticks: { font: { weight: 'bold', size: 10 }, color: textColor, padding: 8 }
            },
            y: {
              min: 0, max: 100,
              grid: { color: gridColor, borderDash: [4, 4] },
              border: { display: false },
              ticks: { font: { weight: 'bold', size: 10 }, color: textColor, callback: v => v + '%', padding: 8 }
            }
          }
        }
      });
    },
    async toggleAutoMode() {
      const newMode = !this.settings.is_auto_mode;
      this.settings.is_auto_mode = newMode; // Optimistic update

      if (this.isBleConnected) {
         // Kirim command AUTO / MANUAL via BLE
         await this.sendBluetoothCommand(newMode ? 'AUTO' : 'MANUAL');
      }

      try {
        await axios.post('/api/update-setting', { is_auto_mode: newMode });
        this.$emit('toast', {
          type: newMode ? 'success' : 'info',
          title: newMode ? 'Kecerdasan Buatan Aktif' : 'Sistem Manual Diambil Alih',
          message: newMode ? 'AI kini memantau cuaca dan mengendalikan rel motor.' : 'Anda memegang kendali penuh atas posisi jemuran.'
        });
        this.fetchData();
      } catch (err) {
        if (!this.isBleConnected) {
           this.settings.is_auto_mode = !newMode; // Rollback
           this.$emit('toast', { type: 'error', title: 'Offline', message: 'Gagal mengubah mode. Tidak ada koneksi internet & Bluetooth.' });
        } else {
           this.$emit('toast', { type: 'success', title: 'Mode Diubah (Offline)', message: 'Sistem diubah via Bluetooth karena internet terputus.' });
        }
      }
    },
    async connectBluetooth() {
      try {
        const serviceUuid = '0000ffe0-0000-1000-8000-00805f9b34fb';
        const characteristicUuid = '0000ffe1-0000-1000-8000-00805f9b34fb';

        this.bleDevice = await navigator.bluetooth.requestDevice({
          filters: [{ services: [serviceUuid] }],
          optionalServices: [serviceUuid]
        });

        this.bleDevice.addEventListener('gattserverdisconnected', this.onBleDisconnected);

        const server = await this.bleDevice.gatt.connect();
        const service = await server.getPrimaryService(serviceUuid);
        this.bleCharacteristic = await service.getCharacteristic(characteristicUuid);
        
        this.isBleConnected = true;
        this.$emit('toast', { type: 'success', title: 'Bluetooth Terhubung', message: 'Koneksi lokal ke ESP32 berhasil.' });
      } catch (error) {
        console.error('Bluetooth Error:', error);
        this.$emit('toast', { type: 'error', title: 'Gagal Koneksi Bluetooth', message: 'Pastikan Bluetooth aktif, perangkat didukung browser, dan ada di dekat Anda.' });
      }
    },
    disconnectBluetooth() {
      if (!this.bleDevice) return;
      if (this.bleDevice.gatt.connected) {
        this.bleDevice.gatt.disconnect();
      } else {
        this.onBleDisconnected();
      }
    },
    onBleDisconnected() {
      this.isBleConnected = false;
      this.bleCharacteristic = null;
      this.bleDevice = null;
      this.$emit('toast', { type: 'info', title: 'Bluetooth Terputus', message: 'Koneksi Bluetooth ke ESP32 telah terputus.' });
    },
    async sendBluetoothCommand(cmdString) {
      if (!this.bleCharacteristic) return;
      try {
        const encoder = new TextEncoder();
        const data = encoder.encode(cmdString + '\n');
        await this.bleCharacteristic.writeValue(data);
        this.$emit('toast', { type: 'success', title: 'Perintah BLE Terkirim', message: `Perintah kontrol dikirim langsung via Bluetooth.` });
      } catch (error) {
        console.error('BLE Send Error:', error);
        this.$emit('toast', { type: 'error', title: 'Gagal Kirim BLE', message: 'Gagal mengirim data. Coba putuskan dan hubungkan ulang Bluetooth.' });
      }
    },
    /**
     * Push perintah ke command queue — ESP32 akan eksekusi dalam ≤10 detik.
     * Jauh lebih responsif dari sebelumnya (dulu harus tunggu interval polling).
     */
    async pushCommand(action) {
      if ((this.settings.is_auto_mode && !this.isBleConnected) || this.isSendingCommand) return;

      if (this.isBleConnected) {
        // Eksekusi Lokal Via Web Bluetooth
        const bleCmd = action === 'move_out' ? 'OUT' : 'IN';
        await this.sendBluetoothCommand(bleCmd);
        
        // Memaksa mode manual di UI secara lokal
        if (this.settings.is_auto_mode) {
           this.settings.is_auto_mode = false;
           // Coba update server di background agar sinkron, abaikan jika gagal
           axios.post('/api/update-setting', { is_auto_mode: false }).catch(() => {});
        }
        return;
      }

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
/* Custom Keyframes for Premium Dark Theme */
@keyframes blob {
  0% { transform: translate(0px, 0px) scale(1); }
  33% { transform: translate(30px, -50px) scale(1.1); }
  66% { transform: translate(-20px, 20px) scale(0.9); }
  100% { transform: translate(0px, 0px) scale(1); }
}
.animate-blob {
  animation: blob 12s infinite alternate cubic-bezier(0.4, 0, 0.2, 1);
}
.animation-delay-2000 { animation-delay: 2s; }
.animation-delay-4000 { animation-delay: 4s; }

@keyframes fade-in-up {
  0% { opacity: 0; transform: translateY(24px) scale(0.98); }
  100% { opacity: 1; transform: translateY(0) scale(1); }
}
.animation-fade-in-up {
  opacity: 0;
  animation: fade-in-up 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes fade-in {
  0% { opacity: 0; transform: scale(0.98); }
  100% { opacity: 1; transform: scale(1); }
}
.animation-fade-in {
  opacity: 0;
  animation: fade-in 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
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

@keyframes glow-pulse {
  0%, 100% { filter: drop-shadow(0 0 4px currentColor); }
  50% { filter: drop-shadow(0 0 12px currentColor); }
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
