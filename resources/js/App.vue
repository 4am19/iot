<template>
  <div v-if="!isAuthReady" class="min-h-screen flex items-center justify-center bg-slate-50">
      <div class="animate-spin w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full"></div>
  </div>

  <LoginVue v-else-if="!isAuthenticated" @login-success="onLoginSuccess" />

  <div v-else class="flex h-screen bg-gradient-to-br from-indigo-50 via-white to-blue-50 font-sans text-slate-800 selection:bg-blue-500 selection:text-white overflow-hidden relative">
    
    <!-- Decorative Background Orbs -->
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-blue-400/10 blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-5%] w-[30%] h-[50%] rounded-full bg-indigo-400/10 blur-[100px] pointer-events-none"></div>

    <!-- Mobile Overlay -->
    <transition enter-active-class="transition-opacity duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition-opacity duration-300" leave-from-class="opacity-100" leave-to-class="opacity-0">
       <div v-if="mobileMenuOpen" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 md:hidden" @click="mobileMenuOpen = false"></div>
    </transition>

    <!-- Sidebar (Glassmorphism) -->
    <aside :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" 
           class="w-72 bg-white/70 backdrop-blur-2xl border-r border-white/50 shadow-[4px_0_24px_rgba(0,0,0,0.02)] flex flex-col flex-shrink-0 z-50 fixed md:relative h-full transition-transform duration-300 ease-[cubic-bezier(0.4,0,0.2,1)]">
      <div class="p-8">
        <h1 class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 tracking-tight flex items-center gap-3">
           <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-500 to-indigo-500 flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
               <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path></svg>
           </div>
           IoT Jemuran
        </h1>
      </div>
      
      <nav class="mt-4 flex-1 px-4 space-y-2">
        <a v-for="item in menuItems" :key="item.key" href="#" @click.prevent="navigateTo(item.key)" 
           :class="activePage === item.key ? 'bg-white shadow-[0_4px_20px_rgba(0,0,0,0.05)] text-blue-600 font-bold border border-gray-100/50' : 'text-slate-500 hover:bg-white/50 hover:text-slate-800 font-medium'" 
           class="block py-3.5 px-5 rounded-2xl transition-all duration-300 group">
          <span class="flex items-center gap-3">
            <span class="transition-transform duration-300 group-hover:scale-110 group-active:scale-95" :class="activePage === item.key ? 'text-blue-500' : 'text-slate-400'" v-html="item.icon"></span>
            {{ item.label }}
          </span>
        </a>
      </nav>
      
      <!-- User Profile Bottom Sidebar -->
      <div class="p-6 mt-auto">
         <div class="bg-white/80 backdrop-blur-md rounded-2xl p-4 shadow-sm border border-slate-100 transition-transform relative group">
            <div class="flex items-center gap-4 cursor-default">
               <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white font-black text-lg shadow-inner ring-2 ring-white">{{ ownerInitials }}</div>
               <div class="flex-1 min-w-0">
                  <p class="font-extrabold text-slate-800 text-sm truncate" :title="ownerName">{{ ownerName }}</p>
                  <p class="text-xs font-semibold flex items-center gap-1.5 mt-0.5" :class="esp32Online ? 'text-emerald-500' : 'text-rose-500'">
                    <span class="w-2 h-2 rounded-full" :class="esp32Online ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500'"></span>
                    {{ esp32Online ? 'System Online' : 'System Offline' }}
                  </p>
               </div>
            </div>
            
            <button @click="logout" class="absolute inset-0 bg-rose-500/90 backdrop-blur-xl rounded-2xl flex items-center justify-center gap-3 text-white font-black opacity-0 group-hover:opacity-100 transition-opacity duration-300">
               <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
               LOGOUT
            </button>
         </div>
      </div>
    </aside>

    <!-- Main Content Shell -->
    <div class="flex-1 flex flex-col relative z-10 w-full h-full overflow-hidden">
      <!-- Top Navbar Glassmorphism -->
      <header class="h-20 bg-white/40 backdrop-blur-2xl flex items-center justify-between px-4 md:px-8 border-b border-white/50 shadow-[0_4px_30px_rgba(0,0,0,0.02)] sticky top-0 z-30">
        
        <div class="flex items-center gap-4">
           <!-- Mobile Hamburger Button -->
           <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2.5 rounded-xl bg-white/80 shadow-sm text-slate-600 hover:bg-white transition-colors active:scale-95">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
           </button>
           <h2 class="text-lg md:text-2xl font-extrabold text-slate-800 tracking-tight capitalize select-none drop-shadow-sm">
             {{ currentPageLabel }}
           </h2>
        </div>
        
        <div class="flex items-center gap-3 md:gap-5">
          <!-- Live Clock -->
          <div class="hidden lg:flex items-center gap-2 px-4 py-2 bg-white/70 backdrop-blur-md border border-white rounded-2xl shadow-sm text-slate-600">
             <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
             <span class="text-sm font-bold tabular-nums tracking-wide">{{ liveTime }}</span>
          </div>

          <!-- ESP32 Status -->
          <div class="hidden sm:flex px-4 py-2 bg-white/70 backdrop-blur-md border border-white rounded-2xl shadow-sm items-center gap-3 hover:bg-white transition-colors cursor-default">
            <div class="relative flex h-3 w-3">
              <span class="absolute inline-flex h-full w-full rounded-full opacity-75" :class="esp32Online ? 'bg-emerald-400 animate-ping' : 'bg-rose-400'"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 shadow-[0_0_8px_rgba(16,185,129,0.8)]" :class="esp32Online ? 'bg-emerald-500' : 'bg-rose-500'"></span>
            </div>
            <span class="text-slate-700 text-[11px] uppercase font-black tracking-widest">{{ esp32Online ? 'ESP32 TERKONEKSI' : 'ESP32 TERPUTUS' }}</span>
          </div>
        </div>
      </header>

      <!-- Dynamic Page Rendering Container -->
      <main class="flex-1 overflow-x-hidden overflow-y-auto w-full relative p-4 md:p-8">
          <transition name="page-slide" mode="out-in">
             <component :is="activePageComponent" @toast="showToast"></component>
          </transition>
      </main>
    </div>

    <!-- Toast Notification System -->
    <transition enter-active-class="transition-all duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)]" enter-from-class="translate-y-4 opacity-0 scale-95" enter-to-class="translate-y-0 opacity-100 scale-100" leave-active-class="transition-all duration-300 ease-in" leave-from-class="translate-y-0 opacity-100 scale-100" leave-to-class="translate-y-2 opacity-0 scale-95">
       <div v-if="toast.visible" class="fixed bottom-8 right-8 z-[100] max-w-sm">
          <div class="flex items-center gap-4 px-6 py-4 rounded-2xl shadow-2xl border backdrop-blur-xl" 
               :class="toast.type === 'success' ? 'bg-emerald-50/90 border-emerald-200 text-emerald-800' : toast.type === 'error' ? 'bg-rose-50/90 border-rose-200 text-rose-800' : 'bg-blue-50/90 border-blue-200 text-blue-800'">
             <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-lg" :class="toast.type === 'success' ? 'bg-emerald-500 text-white' : toast.type === 'error' ? 'bg-rose-500 text-white' : 'bg-blue-500 text-white'">
                {{ toast.type === 'success' ? '✓' : toast.type === 'error' ? '✕' : 'ℹ' }}
             </div>
             <div>
                <p class="font-black text-sm">{{ toast.title }}</p>
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
      toast: {
        visible: false,
        type: 'success',
        title: '',
        message: '',
        timeout: null
      },
      menuItems: [
        { key: 'dashboard', label: 'Tampilan Utama', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>' },
        { key: 'history', label: 'Riwayat Aktivitas', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' },
        { key: 'settings', label: 'Kalibrasi Sensor', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>' },
        { key: 'users', label: 'Akses Keluarga', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>' },
        { key: 'notifications', label: 'Pusat Peringatan', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>' },
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
                // Asumsi created_at berbentuk string seperti "2026-04-08T06:03:23.000000Z"
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
/* Animasi Transisi Halus antar Halaman Sidebar */
.page-slide-enter-active, 
.page-slide-leave-active {
  transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.page-slide-enter-from {
  opacity: 0;
  transform: translateY(15px) scale(0.99);
}
.page-slide-leave-to {
  opacity: 0;
  transform: translateY(-15px) scale(0.99);
}

/* Custom scrollbar untuk webkit */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}
::-webkit-scrollbar-track {
  background: transparent;
}
::-webkit-scrollbar-thumb {
  background: rgba(156, 163, 175, 0.4);
  border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
  background: rgba(107, 114, 128, 0.6);
}
</style>
