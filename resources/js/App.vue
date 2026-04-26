<template>
  <div v-if="!isAuthReady" class="min-h-screen flex items-center justify-center transition-colors duration-500" :class="isDarkMode ? 'bg-[#0a0e1a]' : 'bg-slate-50'">
      <div class="flex flex-col items-center gap-4">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center animate-pulse shadow-lg shadow-indigo-500/30">
          <svg class="w-8 h-8 text-white animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        </div>
        <p class="text-slate-400 text-sm font-semibold tracking-wider animate-pulse">MEMUAT SISTEM...</p>
      </div>
  </div>

  <LoginVue v-else-if="!isAuthenticated" @login-success="onLoginSuccess" />

  <div v-else class="flex h-screen font-sans text-slate-800 dark:text-slate-200 overflow-hidden relative transition-colors duration-500" :class="isDarkMode ? 'bg-[#0a0e1a]' : 'bg-slate-50'">
    
    <!-- Animated Grid Background -->
    <div class="iot-grid-bg"></div>
    
    <!-- Decorative Gradient Orbs -->
    <div class="absolute top-[-15%] left-[-10%] w-[45%] h-[45%] rounded-full bg-indigo-600/8 blur-[120px] pointer-events-none animate-float-slow"></div>
    <div class="absolute bottom-[-15%] right-[-5%] w-[35%] h-[50%] rounded-full bg-purple-600/8 blur-[120px] pointer-events-none animate-float-slow" style="animation-delay: -5s;"></div>
    <div class="absolute top-[40%] left-[30%] w-[25%] h-[25%] rounded-full bg-cyan-600/5 blur-[100px] pointer-events-none animate-float-slow" style="animation-delay: -10s;"></div>

    <!-- Mobile Overlay -->
    <transition enter-active-class="transition-opacity duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition-opacity duration-300" leave-from-class="opacity-100" leave-to-class="opacity-0">
       <div v-if="mobileMenuOpen" class="fixed inset-0 bg-black/60 backdrop-blur-md z-40 md:hidden" @click="mobileMenuOpen = false"></div>
    </transition>

    <!-- Sidebar (Dark Glassmorphism) -->
    <aside :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" 
           class="w-72 flex flex-col flex-shrink-0 z-50 fixed md:relative h-full transition-transform duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] sidebar-glass">
      <div class="p-7 pb-4">
        <h1 class="text-xl font-extrabold tracking-tight flex items-center gap-3">
           <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30 ring-1 ring-white/10">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path></svg>
           </div>
           <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-300 dark:to-purple-300 transition-colors">IoT Jemuran</span>
        </h1>
      </div>
      
      <nav class="mt-2 flex-1 px-4 space-y-1.5">
        <a v-for="item in menuItems" :key="item.key" href="#" @click.prevent="navigateTo(item.key)" 
           :class="activePage === item.key 
              ? 'bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-300 font-bold border-indigo-200 dark:border-indigo-500/30 shadow-sm dark:shadow-[0_0_15px_rgba(99,102,241,0.1)]' 
              : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5 hover:text-slate-800 dark:hover:text-slate-200 border-transparent font-medium'" 
           class="block py-3 px-4 rounded-xl transition-all duration-300 group border">
          <span class="flex items-center gap-3">
            <span class="transition-transform duration-300 group-hover:scale-110 group-active:scale-95" :class="activePage === item.key ? 'text-indigo-500 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500'" v-html="item.icon"></span>
            <span class="text-sm">{{ item.label }}</span>
          </span>
        </a>
      </nav>
      
      <!-- User Profile Bottom Sidebar -->
      <div class="p-5 mt-auto relative z-20">
         <div class="p-4 rounded-[1.25rem] bg-gradient-to-b from-white/80 to-white/40 dark:from-white/[0.05] dark:to-transparent border border-white dark:border-white/10 shadow-[0_8px_30px_rgba(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgba(0,0,0,0.2)] backdrop-blur-xl relative overflow-hidden group transition-all hover:border-indigo-100 dark:hover:border-indigo-500/30">
            
            <!-- Glow Effect -->
            <div class="absolute -right-10 -top-10 w-24 h-24 bg-indigo-500/10 dark:bg-indigo-500/20 rounded-full blur-xl group-hover:bg-indigo-500/20 dark:group-hover:bg-indigo-500/30 transition-colors"></div>

            <div class="flex flex-col gap-3 relative z-10">
               <div class="flex items-center gap-3">
                  <div class="relative flex-shrink-0">
                     <div class="w-10 h-10 bg-gradient-to-tr from-indigo-500 via-purple-500 to-pink-500 rounded-xl flex items-center justify-center text-white font-black text-sm shadow-md ring-2 ring-white dark:ring-slate-900 group-hover:rotate-6 transition-transform duration-300">
                        {{ ownerInitials }}
                     </div>
                     <span class="absolute -bottom-1 -right-1 w-3.5 h-3.5 border-2 border-white dark:border-slate-800 rounded-full" 
                           :class="esp32Online ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.5)]'"
                           :title="esp32Online ? 'System Online' : 'System Offline'">
                     </span>
                  </div>
                  
                  <div class="flex-1 min-w-0">
                     <p class="font-bold text-slate-800 dark:text-slate-100 text-[13px] truncate transition-colors" :title="ownerName">{{ ownerName }}</p>
                     <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest transition-colors">Administrator</p>
                  </div>

                  <button @click="logout" class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800/50 flex items-center justify-center text-slate-400 dark:text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors border border-transparent hover:border-rose-100 dark:hover:border-rose-500/20 flex-shrink-0" title="Keluar">
                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                  </button>
               </div>
               
               <div class="w-full h-px bg-gradient-to-r from-transparent via-slate-200 dark:via-white/10 to-transparent my-1 transition-colors"></div>
               
               <div class="flex justify-between items-center px-1">
                  <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 transition-colors">Status Hardware</span>
                  <span class="text-[9px] font-black tracking-widest px-2 py-0.5 rounded-full transition-colors" 
                        :class="esp32Online ? 'text-emerald-600 bg-emerald-50 dark:text-emerald-400 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20' : 'text-rose-600 bg-rose-50 dark:text-rose-400 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20'">
                     {{ esp32Online ? 'ONLINE' : 'OFFLINE' }}
                  </span>
               </div>
            </div>
         </div>
      </div>
    </aside>

    <!-- Main Content Shell -->
    <div class="flex-1 flex flex-col relative z-10 w-full h-full overflow-hidden">
      <!-- Top Navbar -->
      <header class="h-16 flex items-center justify-between px-4 md:px-7 border-b border-white/5 sticky top-0 z-30 navbar-glass">
        
        <div class="flex items-center gap-3">
           <!-- Mobile Hamburger Button -->
           <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-lg bg-white/50 dark:bg-white/5 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/10 hover:text-slate-800 dark:hover:text-white transition-colors active:scale-95 border border-slate-200 dark:border-white/10">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
           </button>
           <h2 class="text-base md:text-lg font-bold text-slate-800 dark:text-slate-200 tracking-tight capitalize select-none transition-colors">
             {{ currentPageLabel }}
           </h2>
        </div>
        
        <div class="flex items-center gap-2 md:gap-4">
          <!-- Theme Toggle -->
          <button @click="toggleTheme" 
                  class="relative p-2.5 rounded-xl bg-white/80 dark:bg-slate-800/50 backdrop-blur-md border border-slate-200 dark:border-white/10 shadow-sm hover:shadow-md dark:shadow-none dark:hover:shadow-[0_0_15px_rgba(251,191,36,0.15)] transition-all duration-300 group overflow-hidden" 
                  title="Ganti Tema">
             
             <!-- Hover Effect Background -->
             <div class="absolute inset-0 bg-slate-100 dark:bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
             
             <div class="relative z-10 flex items-center justify-center transition-transform duration-700" :class="isDarkMode ? 'rotate-[360deg]' : 'rotate-0'">
                <!-- Sun Icon (shows in dark mode) -->
                <svg v-if="isDarkMode" class="w-5 h-5 text-amber-400 drop-shadow-[0_0_8px_rgba(251,191,36,0.6)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <!-- Moon Icon (shows in light mode) -->
                <svg v-else class="w-5 h-5 text-indigo-500 group-hover:text-indigo-600 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
             </div>
          </button>
          <!-- Live Clock -->
          <div class="hidden lg:flex items-center gap-2 px-3 py-1.5 bg-white/50 dark:bg-white/5 backdrop-blur-md border border-slate-200 dark:border-white/10 rounded-lg text-slate-600 dark:text-slate-400 transition-colors">
             <svg class="w-3.5 h-3.5 text-indigo-500 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
             <span class="text-xs font-semibold tabular-nums tracking-wide">{{ liveTime }}</span>
          </div>

          <!-- ESP32 Status -->
          <div class="hidden sm:flex px-3 py-1.5 bg-white/50 dark:bg-white/5 backdrop-blur-md border border-slate-200 dark:border-white/10 rounded-lg items-center gap-2 cursor-default transition-colors">
            <div class="relative flex h-2 w-2">
              <span class="absolute inline-flex h-full w-full rounded-full opacity-75" :class="esp32Online ? 'bg-emerald-400 animate-ping' : 'bg-rose-400'"></span>
              <span class="relative inline-flex rounded-full h-2 w-2" :class="esp32Online ? 'bg-emerald-400' : 'bg-rose-400'"></span>
            </div>
            <span class="text-[10px] uppercase font-bold tracking-widest" :class="esp32Online ? 'text-emerald-400' : 'text-rose-400'">{{ esp32Online ? 'CONNECTED' : 'OFFLINE' }}</span>
          </div>
        </div>
      </header>

      <!-- Dynamic Page Rendering Container -->
      <main class="flex-1 overflow-x-hidden overflow-y-auto w-full relative p-4 md:p-6 lg:p-8">
          <transition name="page-slide" mode="out-in">
             <component :is="activePageComponent" @toast="showToast" :isDarkMode="isDarkMode"></component>
          </transition>
      </main>
    </div>

    <!-- Toast Notification System -->
    <transition enter-active-class="transition-all duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)]" enter-from-class="translate-y-4 opacity-0 scale-95" enter-to-class="translate-y-0 opacity-100 scale-100" leave-active-class="transition-all duration-300 ease-in" leave-from-class="translate-y-0 opacity-100 scale-100" leave-to-class="translate-y-2 opacity-0 scale-95">
       <div v-if="toast.visible" class="fixed bottom-6 right-6 z-[100] max-w-sm">
          <div class="flex items-center gap-3 px-5 py-3.5 rounded-xl shadow-2xl border backdrop-blur-xl" 
               :class="toast.type === 'success' ? 'bg-emerald-900/80 border-emerald-700/50 text-emerald-200' : toast.type === 'error' ? 'bg-rose-900/80 border-rose-700/50 text-rose-200' : 'bg-indigo-900/80 border-indigo-700/50 text-indigo-200'">
             <div class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold" :class="toast.type === 'success' ? 'bg-emerald-500 text-white' : toast.type === 'error' ? 'bg-rose-500 text-white' : 'bg-indigo-500 text-white'">
                {{ toast.type === 'success' ? '✓' : toast.type === 'error' ? '✕' : 'ℹ' }}
             </div>
             <div>
                <p class="font-bold text-sm">{{ toast.title }}</p>
                <p class="text-xs opacity-80 font-medium mt-0.5">{{ toast.message }}</p>
             </div>
          </div>
       </div>
    </transition>
  </div>
</template>

<script>
import DashboardVue from './Pages/Dashboard.vue';
import HistoryVue from './Pages/History.vue';
import SettingsVue from './Pages/Settings.vue';
import NotificationsVue from './Pages/Notifications.vue';
import UsersVue from './Pages/Users.vue';
import LoginVue from './Pages/Login.vue';
import axios from 'axios';

export default {
  components: { LoginVue },
  data() {
    return {
      activePage: 'dashboard',
      mobileMenuOpen: false,
      liveTime: '',
      clockInterval: null,
      globalPolling: null,
      esp32Online: false,
      ownerName: 'Memuat...',
      isAuthenticated: false,
      isAuthReady: false,
      isDarkMode: true,
      toast: {
        visible: false,
        type: 'success',
        title: '',
        message: '',
        timeout: null
      },
      menuItems: [
        { key: 'dashboard', label: 'Tampilan Utama', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>' },
        { key: 'history', label: 'Riwayat Aktivitas', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' },
        { key: 'settings', label: 'Kalibrasi Sensor', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>' },
        { key: 'users', label: 'Akses Keluarga', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>' },
        { key: 'notifications', label: 'Pusat Peringatan', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>' },
      ]
    }
  },
  computed: {
    activePageComponent() {
       switch(this.activePage) {
          case 'history': return HistoryVue;
          case 'settings': return SettingsVue;
          case 'users': return UsersVue;
          case 'notifications': return NotificationsVue;
          default: return DashboardVue;
       }
    },
    currentPageLabel() {
       const found = this.menuItems.find(i => i.key === this.activePage);
       return found ? found.label : 'Dashboard';
    },
    ownerInitials() {
       return this.ownerName && this.ownerName !== 'Memuat...' ? this.ownerName.charAt(0).toUpperCase() : '?';
    }
  },
  created() {
     axios.interceptors.response.use(
        response => response,
        error => {
           if (error.response && error.response.status === 401 && this.isAuthenticated) {
              this.isAuthenticated = false;
              this.showToast({type: 'error', title: 'Sesi Habis', message: 'Silakan login kembali.'});
           }
           return Promise.reject(error);
        }
     );
  },
  mounted() {
    this.checkAuth();
    this.updateClock();
    this.clockInterval = setInterval(this.updateClock, 1000);
    
    // Load theme preference
    const savedTheme = localStorage.getItem('iot_theme');
    if (savedTheme) {
      this.isDarkMode = savedTheme === 'dark';
    } else {
      this.isDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
    }
    this.applyTheme();
  },
  unmounted() {
    clearInterval(this.clockInterval);
    clearInterval(this.globalPolling);
  },
  methods: {
    navigateTo(page) {
      this.activePage = page;
      this.mobileMenuOpen = false;
    },
    toggleTheme() {
      this.isDarkMode = !this.isDarkMode;
      localStorage.setItem('iot_theme', this.isDarkMode ? 'dark' : 'light');
      this.applyTheme();
    },
    applyTheme() {
      if (this.isDarkMode) {
        document.documentElement.classList.add('dark');
      } else {
        document.documentElement.classList.remove('dark');
      }
    },
    updateClock() {
      const now = new Date();
      this.liveTime = now.toLocaleString('id-ID', { 
        weekday: 'short', day: 'numeric', month: 'short', 
        hour: '2-digit', minute: '2-digit', second: '2-digit' 
      });
    },
    async checkAuth() {
       try {
          const res = await axios.get('/api/auth/check');
          this.isAuthenticated = res.data.authenticated;
          if (this.isAuthenticated) this.startDashboard();
       } catch(e) {
          this.isAuthenticated = false;
       } finally {
          this.isAuthReady = true;
       }
    },
    onLoginSuccess(user) {
       this.isAuthenticated = true;
       this.startDashboard();
       this.showToast({ type: 'success', title: 'Selamat Datang', message: 'Otentikasi berhasil.' });
    },
    async logout() {
       try {
          await axios.post('/api/auth/logout');
          this.isAuthenticated = false;
          clearInterval(this.globalPolling);
          this.showToast({ type: 'info', title: 'Sesi Selesai', message: 'Anda telah keluar dari sistem.' });
       } catch (e) {
          console.error(e);
       }
    },
    startDashboard() {
       this.fetchGlobalData();
       // Hanya jalankan interval jika belum pernah dijalankan agar tidak bertumpuk
       if (!this.globalPolling) {
          this.globalPolling = setInterval(this.fetchGlobalData, 5000);
       }
    },
    showToast({ type = 'success', title = '', message = '' }) {
      if (this.toast.timeout) clearTimeout(this.toast.timeout);
      this.toast.visible = false;
      setTimeout(() => {
        this.toast = { visible: true, type, title, message, timeout: null };
        this.toast.timeout = setTimeout(() => { this.toast.visible = false; }, 3500);
      }, 100);
    },
    async fetchGlobalData() {
       try {
          const response = await axios.get('/api/dashboard-data');
          if (response.data) {
             this.ownerName = response.data.setting.owner_name || 'Administrator';
             
             if (response.data.latestData && response.data.latestData.created_at) {
                // Konversi UTC dari backend ke Date object lokal
                const now = new Date();
                const lastUpdate = new Date(response.data.latestData.created_at);
                const diffSeconds = (now - lastUpdate) / 1000;
                
                // Toleransi 60 detik (karena sistem polling & delay internet)
                this.esp32Online = diffSeconds <= 60;
             } else {
                this.esp32Online = false;
             }
          }
       } catch (e) {
          console.error("Gagal sinkron data global", e);
          this.esp32Online = false;
       }
    }
  }
}
</script>

<style>
/* Sidebar Glass Effect */
.sidebar-glass {
  background: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(24px) saturate(150%);
  border-right: 1px solid rgba(226, 232, 240, 0.6);
  transition: background-color 0.5s, border-color 0.5s;
}
.dark .sidebar-glass {
  background: rgba(10, 14, 30, 0.85);
  border-right: 1px solid rgba(255, 255, 255, 0.06);
}

/* Navbar Glass Effect */
.navbar-glass {
  background: rgba(255, 255, 255, 0.6);
  backdrop-filter: blur(20px) saturate(150%);
  transition: background-color 0.5s;
}
.dark .navbar-glass {
  background: rgba(10, 14, 30, 0.6);
}

/* Animated Grid Background */
.iot-grid-bg {
  position: fixed;
  inset: 0;
  z-index: 0;
  pointer-events: none;
  background-image: 
    linear-gradient(rgba(99, 102, 241, 0.03) 1px, transparent 1px),
    linear-gradient(90deg, rgba(99, 102, 241, 0.03) 1px, transparent 1px);
  background-size: 60px 60px;
  mask-image: radial-gradient(ellipse 80% 60% at 50% 50%, black 40%, transparent 100%);
  -webkit-mask-image: radial-gradient(ellipse 80% 60% at 50% 50%, black 40%, transparent 100%);
}

/* Float Animation for Orbs */
@keyframes float-slow {
  0%, 100% { transform: translateY(0) scale(1); }
  50% { transform: translateY(-30px) scale(1.05); }
}
.animate-float-slow {
  animation: float-slow 20s ease-in-out infinite;
}

/* Page Slide Transition */
.page-slide-enter-active, 
.page-slide-leave-active {
  transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.page-slide-enter-from {
  opacity: 0;
  transform: translateY(12px) scale(0.99);
}
.page-slide-leave-to {
  opacity: 0;
  transform: translateY(-12px) scale(0.99);
}
</style>
