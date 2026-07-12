<template>
  <div class="bg-white/80 dark:bg-slate-900/40 backdrop-blur-xl rounded-3xl p-4 md:p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)] dark:shadow-none border border-slate-200/60 dark:border-white/[0.05] h-[calc(100vh-8rem)] flex flex-col relative overflow-hidden transition-colors duration-500">
     
     <!-- Decorative Background Elements -->
     <div class="absolute top-0 right-0 w-[400px] h-[400px] bg-gradient-to-br from-indigo-500/5 to-purple-500/5 rounded-full blur-[80px] pointer-events-none -translate-y-1/2 translate-x-1/3"></div>
     <div class="absolute bottom-0 left-0 w-[300px] h-[300px] bg-gradient-to-tr from-blue-500/5 to-emerald-500/5 rounded-full blur-[80px] pointer-events-none translate-y-1/3 -translate-x-1/3"></div>

     <!-- Header Section -->
     <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 relative z-50 gap-4">
        <div class="flex items-center gap-3">
           <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
           </div>
           <div>
               <h3 class="text-xl md:text-2xl font-black tracking-tight text-slate-800 dark:text-white transition-colors">Riwayat Aktivitas</h3>
               <p class="text-slate-500 dark:text-slate-400 text-[11px] md:text-xs font-medium mt-0.5 transition-colors">Pantau pergerakan motor dan log sistem.</p>
           </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
           <!-- Filter Dropdown -->
           <div class="relative w-full sm:w-auto">
              <button @click="filterMenuOpen = !filterMenuOpen" @blur="closeFilterMenuDelay" class="flex items-center justify-between w-full sm:w-52 bg-white/80 dark:bg-slate-800/80 px-4 py-2 rounded-xl border border-slate-200 dark:border-white/10 shadow-sm hover:shadow-md transition-all gap-2 group focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
                 <div class="flex items-center gap-2 truncate">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    <span class="text-[11px] md:text-xs font-bold text-slate-700 dark:text-slate-200 truncate">
                       {{ activeFilterLabel }}
                    </span>
                 </div>
                 <svg class="w-4 h-4 text-slate-400 transition-transform duration-300" :class="filterMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
              </button>
              
              <transition
                 enter-active-class="transition duration-200 ease-out"
                 enter-from-class="transform scale-95 opacity-0 -translate-y-2"
                 enter-to-class="transform scale-100 opacity-100 translate-y-0"
                 leave-active-class="transition duration-150 ease-in"
                 leave-from-class="transform scale-100 opacity-100 translate-y-0"
                 leave-to-class="transform scale-95 opacity-0 -translate-y-2"
              >
                 <div v-if="filterMenuOpen" class="absolute z-[60] mt-2 w-full sm:w-56 right-0 sm:right-auto sm:left-0 origin-top bg-white dark:bg-slate-800 rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.15)] dark:shadow-[0_10px_40px_-10px_rgba(0,0,0,0.5)] border border-slate-100 dark:border-slate-700 overflow-hidden">
                    <div class="p-1">
                       <button v-for="filter in availableFilters" :key="filter.id" 
                               @mousedown="selectFilter(filter.id)"
                               class="w-full text-left px-3 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center justify-between group"
                               :class="activeFilter === filter.id ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:pl-4'">
                          {{ filter.label }}
                          <svg v-if="activeFilter === filter.id" class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                       </button>
                    </div>
                 </div>
              </transition>
           </div>
           
           <!-- Export Button -->
           <button @click="exportModalOpen = true" class="bg-indigo-600 hover:bg-indigo-700 text-white flex items-center justify-center px-4 py-1.5 rounded-xl shadow-md shadow-indigo-500/20 transition-all gap-1.5 text-xs font-bold active:scale-95 w-full sm:w-auto">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
              Export
           </button>
        </div>
     </div>

     <!-- Main Content Area -->
     <div class="flex-1 overflow-hidden flex flex-col relative z-10 bg-white/40 dark:bg-slate-800/40 border border-slate-200/50 dark:border-white/5 rounded-2xl shadow-inner backdrop-blur-xl transition-colors">
        
        <!-- Loading State -->
        <div v-if="isLoading" class="absolute inset-0 flex items-center justify-center bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm z-50">
           <div class="flex flex-col items-center gap-4">
              <div class="w-10 h-10 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
              <span class="text-sm font-bold text-slate-500">Menyinkronkan data log...</span>
           </div>
        </div>

        <!-- Empty State -->
        <div v-else-if="filteredLogs.length === 0" class="flex-1 flex flex-col items-center justify-center p-8 text-center opacity-70">
           <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
              <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
           </div>
           <h4 class="text-lg font-bold text-slate-700 dark:text-slate-300">Tidak Ada Aktivitas</h4>
           <p class="text-slate-500 text-sm mt-1 max-w-xs">Belum ada riwayat pergerakan atau data audit yang sesuai dengan filter yang dipilih.</p>
        </div>

        <!-- Timeline / Data List -->
        <div v-else class="flex-1 overflow-y-auto p-4 md:p-8 custom-scrollbar relative">
           
           <!-- Continuous vertical line for timeline -->
           <div class="absolute left-[39px] md:left-[51px] top-8 bottom-8 w-px bg-gradient-to-b from-transparent via-slate-300 dark:via-slate-700 to-transparent"></div>

           <div class="space-y-6 md:space-y-8 relative">
              <transition-group name="list" appear>
                 <div v-for="(log, index) in filteredLogs" :key="log.type + '-' + log.id" class="flex gap-4 md:gap-6 relative group">
                    
                    <!-- Icon / Avatar Indicator -->
                    <div class="relative z-10 flex-shrink-0 mt-1">
                       <!-- Ping animation on the first item -->
                       <div v-if="index === 0" class="absolute inset-0 rounded-full animate-ping opacity-30" :class="getIconConfig(log).bgClass"></div>
                       <div class="w-12 h-12 md:w-14 md:h-14 rounded-2xl flex items-center justify-center text-xl md:text-2xl shadow-lg border-2 border-white dark:border-slate-800 transition-transform duration-300 group-hover:scale-110" :class="[getIconConfig(log).bgClass, getIconConfig(log).textClass]">
                          {{ getIconConfig(log).icon }}
                       </div>
                    </div>

                    <!-- Content Card -->
                    <div class="flex-1 min-w-0 bg-white dark:bg-slate-800/80 rounded-2xl p-4 md:p-5 border border-slate-100 dark:border-slate-700/50 shadow-sm hover:shadow-md dark:shadow-none transition-shadow">
                       <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                          <div class="flex items-center gap-2">
                             <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider" :class="[getIconConfig(log).tagBg, getIconConfig(log).textClass]">
                                {{ getIconConfig(log).tag }}
                             </span>
                             <span class="text-xs font-bold text-slate-400 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ formatRelativeTime(log.created_at) }}
                             </span>
                          </div>
                          <span class="text-xs font-semibold text-slate-500">{{ formatAbsoluteTime(log.created_at) }}</span>
                       </div>

                       <!-- Content Payload for Sensor Movement -->
                       <div v-if="log.type === 'pergerakan'" class="flex flex-col gap-3">
                          <p class="text-slate-700 dark:text-slate-200 font-medium text-sm md:text-base leading-snug">
                             Motor bergerak mengubah posisi jemuran menjadi <span class="font-bold px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-700">{{ log.clothesline_status }}</span>
                          </p>
                          
                          <!-- Sensor Metrics Grid -->
                          <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mt-2">
                             <div class="bg-slate-50 dark:bg-slate-900/50 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800 flex items-center gap-3">
                                <span class="text-lg">🌤️</span>
                                <div>
                                   <p class="text-[10px] font-bold text-slate-400 uppercase">Cuaca</p>
                                   <p class="text-xs font-bold text-slate-700 dark:text-slate-300 truncate">{{ log.weather_condition }}</p>
                                </div>
                             </div>
                             <div class="bg-slate-50 dark:bg-slate-900/50 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800 flex items-center gap-3">
                                <span class="text-lg">☀️</span>
                                <div>
                                   <p class="text-[10px] font-bold text-slate-400 uppercase">LDR</p>
                                   <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ log.ldr_value }}%</p>
                                </div>
                             </div>
                             <div class="bg-slate-50 dark:bg-slate-900/50 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800 flex items-center gap-3 col-span-2 sm:col-span-1">
                                <span class="text-lg">🌧️</span>
                                <div>
                                   <p class="text-[10px] font-bold text-slate-400 uppercase">Hujan</p>
                                   <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ log.rain_percentage }}%</p>
                                </div>
                             </div>
                          </div>
                       </div>

                       <!-- Content Payload for Audit Log -->
                       <div v-else-if="log.type === 'audit'" class="flex flex-col gap-3">
                          <p class="text-slate-700 dark:text-slate-200 font-medium text-sm md:text-base leading-snug">
                             Pengguna <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ log.user?.name || 'Sistem' }}</span> tercatat melakukan aksi: 
                             <span class="font-bold px-2 py-0.5 rounded ml-1" 
                                   :class="log.action.toLowerCase().includes('login') ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : log.action.toLowerCase().includes('logout') ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'">
                                {{ log.action }}
                             </span>
                          </p>
                          <div class="flex flex-wrap gap-3 mt-1">
                             <div class="flex items-center gap-1.5 text-xs text-slate-500 bg-slate-50 dark:bg-slate-900/50 px-2 py-1 rounded-md border border-slate-100 dark:border-slate-800">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                {{ log.ip_address || 'IP tidak terdeteksi' }}
                             </div>
                             <div class="flex items-center gap-1.5 text-xs text-slate-500 bg-slate-50 dark:bg-slate-900/50 px-2 py-1 rounded-md border border-slate-100 dark:border-slate-800 max-w-[200px] truncate" :title="log.user_agent">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                {{ log.user_agent || 'Perangkat tidak terdeteksi' }}
                             </div>
                          </div>
                       </div>

                    </div>
                 </div>
              </transition-group>
           </div>
        </div>

     <!-- Export Modal Overlay -->
     <Teleport to="body">
        <transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
           <div v-if="exportModalOpen" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" @click="exportModalOpen = false">
              
              <transition enter-active-class="transition duration-300 ease-out delay-75" enter-from-class="opacity-0 translate-y-8 scale-95" enter-to-class="opacity-100 translate-y-0 scale-100" leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100 translate-y-0 scale-100" leave-to-class="opacity-0 translate-y-4 scale-95">
                 <div v-if="exportModalOpen" class="bg-white dark:bg-slate-800 w-full max-w-md rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-700 max-h-[90vh] flex flex-col" @click.stop>
                    
                    <!-- Modal Header -->
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700/50 flex justify-between items-center bg-white dark:bg-slate-800 rounded-t-3xl">
                       <h4 class="font-black text-xl text-slate-800 dark:text-white flex items-center gap-3">
                          <div class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                          </div>
                          Export Laporan
                       </h4>
                       <button @click="exportModalOpen = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 flex items-center justify-center text-slate-500 dark:text-slate-400 transition-colors">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                       </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 space-y-6 bg-white dark:bg-slate-800 overflow-y-auto custom-scrollbar flex-1">
                       
                       <!-- Format File Selection -->
                       <div>
                          <label class="block text-[13px] font-bold text-slate-700 dark:text-slate-300 mb-3">Format File</label>
                          <div class="grid grid-cols-2 gap-4">
                             <!-- PDF Option -->
                             <button @click="exportConfig.format = 'pdf'" class="flex flex-col items-center justify-center gap-2 p-4 rounded-2xl border-2 transition-all" :class="exportConfig.format === 'pdf' ? 'border-emerald-500 bg-emerald-50/30 dark:bg-emerald-500/5 shadow-md shadow-emerald-500/10' : 'border-slate-100 dark:border-slate-700 hover:border-slate-200 dark:hover:border-slate-600 bg-white dark:bg-slate-800'">
                                <img src="/images/pdf-icon.png" alt="PDF" class="w-11 h-11 mb-1 transition-all duration-300" :class="exportConfig.format === 'pdf' ? 'scale-110 opacity-100' : 'opacity-50 grayscale'">
                                <div class="text-center mt-1">
                                   <div class="font-bold text-slate-800 dark:text-white text-sm">PDF</div>
                                   <div class="text-[11px] font-semibold text-slate-400">Laporan Rapi</div>
                                </div>
                             </button>

                             <!-- Excel Option -->
                             <button @click="exportConfig.format = 'excel'" class="flex flex-col items-center justify-center gap-2 p-4 rounded-2xl border-2 transition-all" :class="exportConfig.format === 'excel' ? 'border-emerald-500 bg-emerald-50/30 dark:bg-emerald-500/5 shadow-md shadow-emerald-500/10' : 'border-slate-100 dark:border-slate-700 hover:border-slate-200 dark:hover:border-slate-600 bg-white dark:bg-slate-800'">
                                <img src="/images/excel-icon.png" alt="Excel" class="w-11 h-11 mb-1 transition-all duration-300" :class="exportConfig.format === 'excel' ? 'scale-110 opacity-100' : 'opacity-50 grayscale'">
                                <div class="text-center mt-1">
                                   <div class="font-bold text-slate-800 dark:text-white text-sm">Excel (CSV)</div>
                                   <div class="text-[11px] font-semibold text-slate-400">Raw Data</div>
                                </div>
                             </button>
                          </div>
                       </div>
                                        <!-- Kategori Data -->
                    <div class="relative z-20">
                       <label class="block text-[13px] font-bold text-slate-700 dark:text-slate-300 mb-2">Kategori Data</label>
                       <button @click="exportTypeMenuOpen = !exportTypeMenuOpen" @blur="closeExportTypeMenuDelay" class="flex items-center justify-between w-full bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-xl px-4 py-3 text-sm font-bold focus:outline-none focus:border-emerald-500 transition-colors">
                          <span>{{ exportTypeLabel }}</span>
                          <svg class="w-4 h-4 text-slate-400 transition-transform duration-300" :class="exportTypeMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                       </button>
                       <transition enter-active-class="transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)]" enter-from-class="opacity-0 -translate-y-2 max-h-0" enter-to-class="opacity-100 translate-y-0 max-h-64" leave-active-class="transition-all duration-200 ease-in-out" leave-from-class="opacity-100 translate-y-0 max-h-64" leave-to-class="opacity-0 -translate-y-2 max-h-0">
                          <div v-if="exportTypeMenuOpen" class="absolute z-50 top-full left-0 right-0 mt-2 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-100 dark:border-slate-700 overflow-hidden">
                             <div class="p-1.5 space-y-1">
                                <button v-for="filter in availableFilters" :key="filter.id" @mousedown.prevent="selectExportType(filter.id)" class="w-full text-left px-3 py-3 rounded-lg text-xs font-bold transition-all flex items-center justify-between group" :class="exportConfig.type === filter.id ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:pl-4'">
                                   {{ filter.label }}
                                   <svg v-if="exportConfig.type === filter.id" class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </button>
                             </div>
                          </div>
                       </transition>
                    </div>

                    <!-- Rentang Waktu -->
                    <div class="relative z-10">
                       <label class="block text-[13px] font-bold text-slate-700 dark:text-slate-300 mb-2">Rentang Waktu</label>
                       <button @click="exportDateMenuOpen = !exportDateMenuOpen" @blur="closeExportDateMenuDelay" class="flex items-center justify-between w-full bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-xl px-4 py-3 text-sm font-bold focus:outline-none focus:border-emerald-500 transition-colors" :class="exportDateMenuOpen ? 'mb-0' : 'mb-3'">
                          <span>{{ exportDateRangeLabel }}</span>
                          <svg class="w-4 h-4 text-slate-400 transition-transform duration-300" :class="exportDateMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                       </button>
                       <transition enter-active-class="transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)]" enter-from-class="opacity-0 -translate-y-2 max-h-0" enter-to-class="opacity-100 translate-y-0 max-h-64" leave-active-class="transition-all duration-200 ease-in-out" leave-from-class="opacity-100 translate-y-0 max-h-64" leave-to-class="opacity-0 -translate-y-2 max-h-0">
                          <div v-if="exportDateMenuOpen" class="absolute z-50 top-full left-0 right-0 mt-2 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-100 dark:border-slate-700 overflow-hidden">
                             <div class="p-1.5 space-y-1">
                                <button v-for="range in [{id: 'all', label: 'Semua Waktu'}, {id: 'today', label: 'Hari Ini'}, {id: '7days', label: '7 Hari Terakhir'}, {id: 'custom', label: 'Custom Tanggal'}]" :key="range.id" @mousedown.prevent="selectExportDateRange(range.id)" class="w-full text-left px-3 py-3 rounded-lg text-xs font-bold transition-all flex items-center justify-between group" :class="exportConfig.dateRange === range.id ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:pl-4'">
                                   {{ range.label }}
                                   <svg v-if="exportConfig.dateRange === range.id" class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </button>
                             </div>
                          </div>
                       </transition>

                          <transition enter-active-class="transition-all duration-300" enter-from-class="opacity-0 -translate-y-2 max-h-0 overflow-hidden" enter-to-class="opacity-100 translate-y-0 max-h-40 overflow-hidden" leave-active-class="transition-all duration-200" leave-from-class="opacity-100 translate-y-0 max-h-40 overflow-hidden" leave-to-class="opacity-0 -translate-y-2 max-h-0 overflow-hidden">
                             <div v-if="exportConfig.dateRange === 'custom'" class="grid grid-cols-2 gap-3">
                                <div>
                                   <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 ml-1">Dari Tanggal</label>
                                   <input type="date" v-model="exportConfig.startDate" class="w-full bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-xl px-3 py-2.5 text-xs font-semibold focus:outline-none focus:border-emerald-500 transition-colors">
                                </div>
                                <div>
                                   <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5 ml-1">Sampai Tanggal</label>
                                   <input type="date" v-model="exportConfig.endDate" class="w-full bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-xl px-3 py-2.5 text-xs font-semibold focus:outline-none focus:border-emerald-500 transition-colors">
                                </div>
                             </div>
                          </transition>
                       </div>

                    </div>

                    <!-- Modal Footer Actions -->
                    <div class="px-6 pb-6 bg-white dark:bg-slate-800 rounded-b-3xl">
                       <button @click="doExport" class="w-full flex items-center justify-center gap-2 bg-[#00a884] hover:bg-[#008f6f] text-white py-3.5 rounded-xl font-bold transition-all active:scale-95 shadow-lg shadow-[#00a884]/30">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                          DOWNLOAD FILE
                       </button>
                    </div>
                 </div>
              </transition>
           </div>
        </transition>
     </Teleport>

    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { jsPDF } from 'jspdf';
import 'jspdf-autotable';
import * as XLSX from 'xlsx';

export default {
  emits: ['toast'],
  props: {
    currentUser: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      rawLogs: [],
      isLoading: true,
      activeFilter: 'all',
      filterMenuOpen: false,
      exportModalOpen: false,
      exportTypeMenuOpen: false,
      exportDateMenuOpen: false,
      exportConfig: {
         type: 'all',
         dateRange: 'all',
         format: 'pdf',
         startDate: '',
         endDate: ''
      },
      polling: null
    }
  },
  computed: {
    isAdmin() {
      return this.currentUser?.role === 'admin';
    },
    availableFilters() {
      const filters = [
        { id: 'all', label: 'Semua Aktivitas' },
        { id: 'otomatis', label: 'Otomatis (Sensor)' },
        { id: 'manual_dashboard', label: 'Manual (Dashboard)' },
        { id: 'manual_fisik', label: 'Manual (Push Button)' },
      ];
      if (this.isAdmin) {
        filters.push({ id: 'audit', label: 'Log Autentikasi' });
      }
      return filters;
    },
    activeFilterLabel() {
       const found = this.availableFilters.find(f => f.id === this.activeFilter);
       return found ? found.label : 'Filter Data';
    },
    exportTypeLabel() {
       const found = this.availableFilters.find(f => f.id === this.exportConfig.type);
       return found ? found.label : 'Semua Aktivitas';
    },
    exportDateRangeLabel() {
       const options = {
          'all': 'Semua Waktu',
          'today': 'Hari Ini',
          '7days': '7 Hari Terakhir',
          'custom': 'Custom Tanggal'
       };
       return options[this.exportConfig.dateRange] || 'Semua Waktu';
    },
    filteredLogs() {
      if (this.activeFilter === 'all') {
         // Default sembunyikan audit dari tab all, biarkan audit hanya di tab audit
         // Atau bisa juga tampilkan di all jika admin. Kita tampilkan jika admin.
         return this.isAdmin ? this.rawLogs : this.rawLogs.filter(l => l.type !== 'audit');
      }
      
      if (this.activeFilter === 'audit') {
         return this.rawLogs.filter(l => l.type === 'audit');
      }

      // Filter berdasarkan trigger_source untuk pergerakan
      return this.rawLogs.filter(l => l.type === 'pergerakan' && l.trigger_source === this.activeFilter);
    }
  },
  mounted() {
    this.fetchLogs();
    this.polling = setInterval(this.fetchLogs, 3000);
  },
  unmounted() {
    clearInterval(this.polling);
  },
  methods: {
    selectFilter(filterId) {
      this.activeFilter = filterId;
      this.filterMenuOpen = false;
    },
    closeFilterMenuDelay() {
      setTimeout(() => {
        this.filterMenuOpen = false;
      }, 200);
    },
    selectExportType(type) {
       this.exportConfig.type = type;
       this.exportTypeMenuOpen = false;
    },
    selectExportDateRange(range) {
       this.exportConfig.dateRange = range;
       this.exportDateMenuOpen = false;
    },
    closeExportTypeMenuDelay() {
       setTimeout(() => { this.exportTypeMenuOpen = false; }, 200);
    },
    closeExportDateMenuDelay() {
       setTimeout(() => { this.exportDateMenuOpen = false; }, 200);
    },
    getIconConfig(log) {
       if (log.type === 'audit') {
          return {
             icon: '🔐',
             bgClass: 'bg-emerald-100 dark:bg-emerald-900/50',
             textClass: 'text-emerald-700 dark:text-emerald-400',
             tagBg: 'bg-emerald-200/50 dark:bg-emerald-800/50',
             tag: 'Keamanan'
          };
       }

       // Untuk tipe pergerakan
       switch (log.trigger_source) {
          case 'otomatis':
             return {
                icon: '🤖',
                bgClass: 'bg-blue-100 dark:bg-blue-900/50',
                textClass: 'text-blue-700 dark:text-blue-400',
                tagBg: 'bg-blue-200/50 dark:bg-blue-800/50',
                tag: 'AI Otomatis'
             };
          case 'manual_dashboard':
             return {
                icon: '💻',
                bgClass: 'bg-purple-100 dark:bg-purple-900/50',
                textClass: 'text-purple-700 dark:text-purple-400',
                tagBg: 'bg-purple-200/50 dark:bg-purple-800/50',
                tag: 'Kendali Web'
             };
          case 'manual_fisik':
             return {
                icon: '🔘',
                bgClass: 'bg-amber-100 dark:bg-amber-900/50',
                textClass: 'text-amber-700 dark:text-amber-400',
                tagBg: 'bg-amber-200/50 dark:bg-amber-800/50',
                tag: 'Tombol Fisik'
             };
          default:
             return {
                icon: '⚙️',
                bgClass: 'bg-slate-100 dark:bg-slate-800',
                textClass: 'text-slate-700 dark:text-slate-300',
                tagBg: 'bg-slate-200/50 dark:bg-slate-700/50',
                tag: 'Sistem'
             };
       }
    },
    formatAbsoluteTime(dateString) {
      if(!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleString('id-ID', {day: 'numeric', month:'short', year:'numeric', hour: '2-digit', minute:'2-digit'});
    },
    formatRelativeTime(dateString) {
       if(!dateString) return '';
       const date = new Date(dateString);
       const now = new Date();
       const diffInSeconds = Math.floor((now - date) / 1000);
       
       if (diffInSeconds < 60) return 'Baru saja';
       if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} mnt yang lalu`;
       if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} jam yang lalu`;
       return `${Math.floor(diffInSeconds / 86400)} hari yang lalu`;
    },
    async fetchLogs() {
      try {
        const response = await axios.get('/api/activity-logs');
        if(response.data) {
          this.rawLogs = response.data;
        }
      } catch (error) { 
         console.error("Gagal get activity logs", error); 
      } finally { 
         this.isLoading = false; 
      }
    },
    getFilteredExportData() {
       let data = this.rawLogs;
       
       if (this.exportConfig.type !== 'all') {
          if (this.exportConfig.type === 'audit') {
             data = data.filter(l => l.type === 'audit');
          } else if (this.exportConfig.type === 'otomatis') {
             data = data.filter(l => l.type === 'pergerakan' && l.trigger_source === 'otomatis');
          } else if (this.exportConfig.type === 'manual_dashboard') {
             data = data.filter(l => l.type === 'pergerakan' && l.trigger_source === 'manual_dashboard');
          } else if (this.exportConfig.type === 'manual_fisik') {
             data = data.filter(l => l.type === 'pergerakan' && l.trigger_source === 'manual_fisik');
          }
       } else if (!this.isAdmin) {
          data = data.filter(l => l.type !== 'audit');
       }

       if (this.exportConfig.dateRange !== 'all') {
          const now = new Date();
          const target = new Date();
          if (this.exportConfig.dateRange === 'today') {
             target.setHours(0,0,0,0);
             data = data.filter(l => new Date(l.created_at) >= target);
          } else if (this.exportConfig.dateRange === '7days') {
             target.setDate(now.getDate() - 7);
             data = data.filter(l => new Date(l.created_at) >= target);
          } else if (this.exportConfig.dateRange === 'custom') {
             const start = this.exportConfig.startDate ? new Date(this.exportConfig.startDate) : new Date(0);
             const end = this.exportConfig.endDate ? new Date(this.exportConfig.endDate) : new Date();
             end.setHours(23,59,59,999);
             data = data.filter(l => {
                const date = new Date(l.created_at);
                return date >= start && date <= end;
             });
          }
       }
       return data;
    },
    doExport() {
       const format = this.exportConfig.format;
       const data = this.getFilteredExportData();
       if (data.length === 0) {
          this.$emit('toast', { type: 'error', title: 'Data Kosong', message: 'Tidak ada data untuk filter ini.' });
          return;
       }

       let headers = [];
       let rows = [];

       if (this.exportConfig.type === 'audit') {
          headers = ['Waktu', 'Pengguna', 'Aksi', 'Alamat IP', 'Perangkat (User Agent)'];
          rows = data.map(l => [
             this.formatAbsoluteTime(l.created_at),
             l.user?.name || 'Unknown',
             l.action,
             l.ip_address || '-',
             l.user_agent || '-'
          ]);
       } else if (['pergerakan', 'otomatis', 'manual_dashboard', 'manual_fisik'].includes(this.exportConfig.type)) {
          headers = ['Waktu', 'Status Jemuran', 'Pemicu (Trigger)', 'Kondisi Cuaca', 'Sensor Cahaya', 'Sensor Hujan'];
          rows = data.map(l => [
             this.formatAbsoluteTime(l.created_at),
             (l.clothesline_status || '').toUpperCase(),
             (l.trigger_source || '-').toUpperCase(),
             (l.weather_condition || '-').toUpperCase(),
             `${l.ldr_value || 0}%`,
             `${l.rain_percentage || 0}%`
          ]);
       } else {
          // Gabungan (Semua Aktivitas)
          headers = ['Waktu', 'Kategori', 'Detail Aktivitas / Status', 'Sumber / IP', 'Data Tambahan'];
          rows = data.map(l => {
             if (l.type === 'audit') {
                return [
                   this.formatAbsoluteTime(l.created_at),
                   'KEAMANAN',
                   `Pengguna: ${l.user?.name || 'Sistem'} melalukan ${l.action}`,
                   l.ip_address || '-',
                   l.user_agent || '-'
                ];
             } else {
                return [
                   this.formatAbsoluteTime(l.created_at),
                   'PERGERAKAN',
                   `Posisi Motor: ${(l.clothesline_status || '-').toUpperCase()}`,
                   `Pemicu: ${(l.trigger_source || '-').toUpperCase()}`,
                   `Cuaca: ${l.weather_condition || '-'} | Cahaya: ${l.ldr_value || 0}% | Hujan: ${l.rain_percentage || 0}%`
                ];
             }
          });
       }

       if (format === 'excel') {
          const ws = XLSX.utils.aoa_to_sheet([headers, ...rows]);
          // Add some simple column widths for excel
          ws['!cols'] = headers.map(() => ({ wch: 25 }));
          const wb = XLSX.utils.book_new();
          XLSX.utils.book_append_sheet(wb, ws, "Riwayat Aktivitas");
          XLSX.writeFile(wb, `Laporan_IoT_Jemuran_${Date.now()}.xlsx`);
       } else if (format === 'pdf') {
          const doc = new jsPDF('landscape'); // Landscape to fit data better
          
          // Header PDF Professional
          doc.setFontSize(18);
          doc.setTextColor(30, 41, 59);
          doc.text("Laporan Riwayat Aktivitas - Smart Clothesline", 14, 22);
          
          doc.setFontSize(10);
          doc.setTextColor(100, 116, 139);
          doc.text(`Dicetak pada: ${this.formatAbsoluteTime(new Date().toISOString())}`, 14, 30);
          doc.text(`Kategori Data: ${this.exportTypeLabel}`, 14, 36);
          doc.text(`Rentang Waktu: ${this.exportDateRangeLabel}`, 14, 42);

          doc.autoTable({
             head: [headers],
             body: rows,
             startY: 50,
             theme: 'striped',
             headStyles: { fillColor: [16, 185, 129], textColor: 255, fontStyle: 'bold' },
             styles: { fontSize: 9, cellPadding: 4, textColor: [51, 65, 85] },
             alternateRowStyles: { fillColor: [248, 250, 252] }
          });
          doc.save(`Laporan_IoT_Jemuran_${Date.now()}.pdf`);
       }

       this.exportModalOpen = false;
       this.$emit('toast', { type: 'success', title: 'Berhasil!', message: `File ${format.toUpperCase()} telah diunduh.` });
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
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(203, 213, 225, 0.4);
  border-radius: 10px;
}
:global(.dark) .custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.1);
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(148, 163, 184, 0.8);
}
:global(.dark) .custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.2);
}

.list-enter-active,
.list-leave-active {
  transition: all 0.5s ease;
}
.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(-30px);
}
</style>
