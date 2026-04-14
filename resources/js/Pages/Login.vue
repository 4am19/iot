<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-50 via-white to-blue-50 font-sans p-4 relative overflow-hidden">
     <!-- Decorative Background Orbs -->
     <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-blue-400/20 blur-[100px] pointer-events-none"></div>
     <div class="absolute bottom-[-10%] right-[-5%] w-[30%] h-[50%] rounded-full bg-indigo-400/20 blur-[100px] pointer-events-none"></div>

     <div class="w-full max-w-md relative z-10 transition-all transform hover:scale-[1.01] duration-500">
        <div class="bg-white/70 backdrop-blur-2xl rounded-[2.5rem] p-10 md:p-12 shadow-[0_20px_50px_rgba(0,0,0,0.05)] border border-white relative overflow-hidden">
           
           <div class="absolute top-0 right-0 w-32 h-32 bg-blue-100 opacity-30 rounded-full -mr-16 -mt-16 blur-xl"></div>
           
           <div class="text-center mb-10">
              <div class="mx-auto w-16 h-16 rounded-[1.5rem] bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/30 mb-6 group-hover:scale-110 transition-transform duration-500">
                  <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path></svg>
              </div>
              <h1 class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-slate-800 to-slate-600 tracking-tight">AI Control Center</h1>
              <p class="text-slate-400 mt-2 font-medium text-sm">Masuk untuk mengambil kendali pusat perangkat IoT otomatisasi rumah Anda.</p>
           </div>

           <form @submit.prevent="login" class="space-y-6 relative z-10">
              
              <div v-if="errorMessage" class="bg-rose-50 border border-rose-100 text-rose-600 px-5 py-4 rounded-2xl text-sm font-bold flex items-center gap-3 animate-[pulse_2s_ease-in-out_infinite]">
                 <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                 <span>{{ errorMessage }}</span>
              </div>

              <div class="space-y-2 relative">
                 <label class="text-xs font-black text-slate-400 uppercase tracking-widest pl-2">Email Administrator</label>
                 <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                       <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                    </div>
                    <input type="email" v-model="form.email" required
                           class="w-full bg-white/50 border border-slate-200 text-slate-800 rounded-2xl pl-12 pr-4 py-4 font-bold focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-inner placeholder:font-normal placeholder:text-slate-400"
                           placeholder="admin@iot.com">
                 </div>
              </div>

              <div class="space-y-2 relative">
                 <label class="text-xs font-black text-slate-400 uppercase tracking-widest pl-2">Sandi Sistem</label>
                 <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                       <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <input type="password" v-model="form.password" required
                           class="w-full bg-white/50 border border-slate-200 text-slate-800 rounded-2xl pl-12 pr-4 py-4 font-bold focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-inner placeholder:font-normal placeholder:text-slate-400"
                           placeholder="••••••••">
                 </div>
              </div>

              <button type="submit" :disabled="isLoading"
                      class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-black text-lg py-4 rounded-2xl shadow-[0_10px_30px_rgba(37,99,235,0.3)] hover:shadow-[0_10px_40px_rgba(37,99,235,0.4)] disabled:opacity-70 disabled:cursor-not-allowed transition-all hover:scale-[1.02] active:scale-[0.98] mt-6 flex items-center justify-center gap-2 relative overflow-hidden group">
                 <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-[150%] skew-x-[-20deg] group-hover:translate-x-[150%] transition-transform duration-700"></div>
                 <svg v-if="isLoading" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                 <span>{{ isLoading ? 'MENGOTENTIKASI...' : 'MASUK SISTEM' }}</span>
              </button>
           </form>
           
           <div class="mt-8 text-center text-xs font-bold text-slate-400 uppercase tracking-widest relative z-10">
              Standar Keamanan IoT Profesional
           </div>
        </div>
     </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  emits: ['login-success'],
  data() {
    return {
      form: {
        email: '',
        password: ''
      },
      isLoading: false,
      errorMessage: ''
    }
  },
  mounted() {
     // Fetch sanctum CSRF cookies first since we use stateful session authentication
     axios.get('/sanctum/csrf-cookie').catch(() => {});
  },
  methods: {
    async login() {
       this.isLoading = true;
       this.errorMessage = '';
       
       try {
          await axios.get('/sanctum/csrf-cookie'); // Ensure CSRF token
          const response = await axios.post('/api/auth/login', this.form);
          
          if (response.data.success) {
             this.$emit('login-success', response.data.user);
          }
       } catch (error) {
          if (error.response && error.response.status === 401) {
             this.errorMessage = 'Email atau sandi sistem salah!';
          } else {
             this.errorMessage = 'Gagal terhubung ke server autentikasi.';
          }
       } finally {
          this.isLoading = false;
       }
    }
  }
}
</script>
