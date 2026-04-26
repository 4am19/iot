<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-50 via-white to-blue-50 dark:from-[#0a0e1a] dark:via-[#0f172a] dark:to-[#1e1b4b] font-sans p-4 relative overflow-hidden transition-colors duration-500">
     
     <!-- Decorative Background Orbs (Animated) -->
     <div class="absolute top-[-10%] left-[-10%] w-[50vw] h-[50vw] max-w-[600px] max-h-[600px] rounded-full bg-blue-400/20 dark:bg-indigo-500/20 blur-[80px] md:blur-[120px] pointer-events-none animate-[pulse_8s_ease-in-out_infinite] transition-colors"></div>
     <div class="absolute bottom-[-10%] right-[-5%] w-[40vw] h-[60vw] max-w-[500px] max-h-[700px] rounded-full bg-indigo-400/20 dark:bg-purple-500/20 blur-[80px] md:blur-[120px] pointer-events-none animate-[pulse_10s_ease-in-out_infinite_reverse] transition-colors"></div>
     <div class="absolute top-[20%] right-[10%] w-[30vw] h-[30vw] max-w-[400px] max-h-[400px] rounded-full bg-teal-400/10 dark:bg-emerald-500/10 blur-[80px] pointer-events-none animate-[pulse_12s_ease-in-out_infinite] transition-colors"></div>

     <div class="w-full max-w-md relative z-10 transition-all transform hover:-translate-y-1 duration-500">
        <!-- Floating Glassmorphism Card -->
        <div class="bg-white/70 dark:bg-[#0f172a]/60 backdrop-blur-2xl rounded-[2.5rem] p-8 md:p-12 shadow-[0_20px_50px_rgba(0,0,0,0.05)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] border border-white dark:border-white/10 relative overflow-hidden transition-all group">
           
           <!-- Card inner highlight -->
           <div class="absolute inset-0 bg-gradient-to-b from-white/40 to-transparent dark:from-white/5 dark:to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
           
           <div class="absolute top-0 right-0 w-32 h-32 bg-blue-100 dark:bg-indigo-500/20 opacity-30 rounded-full -mr-16 -mt-16 blur-xl transition-colors"></div>
           
           <!-- Header / Logo -->
           <div class="text-center mb-10 relative z-10">
              <div class="mx-auto w-20 h-20 rounded-[1.8rem] bg-gradient-to-tr from-blue-500 to-indigo-600 p-[2px] shadow-xl shadow-blue-500/30 mb-6 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500">
                 <div class="w-full h-full bg-white dark:bg-[#0a0e1a] rounded-[1.7rem] flex items-center justify-center transition-colors">
                    <svg class="w-10 h-10 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path></svg>
                 </div>
              </div>
              <h1 class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-slate-800 to-slate-600 dark:from-white dark:to-slate-300 tracking-tight transition-colors">AI Control Center</h1>
              <p class="text-slate-500 dark:text-slate-400 mt-3 font-medium text-sm transition-colors">Masuk untuk mengambil kendali pusat perangkat IoT otomatisasi rumah Anda.</p>
           </div>

           <!-- Form -->
           <form @submit.prevent="login" class="space-y-6 relative z-10">
              
              <!-- Error State -->
              <transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 -translate-y-2">
                 <div v-if="errorMessage" class="bg-rose-50 dark:bg-rose-500/10 border border-rose-100 dark:border-rose-500/20 text-rose-600 dark:text-rose-400 px-5 py-4 rounded-2xl text-sm font-bold flex items-center gap-3 shadow-sm transition-colors">
                    <svg class="w-5 h-5 flex-shrink-0 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ errorMessage }}</span>
                 </div>
              </transition>

              <!-- Inputs -->
              <div class="space-y-5">
                 <div class="space-y-2 relative group/input">
                    <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest pl-2 transition-colors">Email Administrator</label>
                    <div class="relative">
                       <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500 transition-colors group-focus-within/input:text-indigo-500 dark:group-focus-within/input:text-indigo-400">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                       </div>
                       <input type="email" v-model="form.email" required
                              class="w-full bg-white/50 dark:bg-[#0a0e1a]/50 border border-slate-200 dark:border-white/10 text-slate-800 dark:text-white rounded-2xl pl-12 pr-4 py-4 font-bold focus:outline-none focus:ring-4 focus:ring-indigo-500/20 dark:focus:ring-indigo-500/30 focus:border-indigo-500 dark:focus:border-indigo-400 transition-all shadow-inner dark:shadow-none placeholder:font-normal placeholder:text-slate-400 dark:placeholder:text-slate-600"
                              placeholder="admin@iot.com">
                    </div>
                 </div>

                 <div class="space-y-2 relative group/input">
                    <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest pl-2 transition-colors">Sandi Sistem</label>
                    <div class="relative">
                       <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500 transition-colors group-focus-within/input:text-indigo-500 dark:group-focus-within/input:text-indigo-400">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                       </div>
                       <input type="password" v-model="form.password" required
                              class="w-full bg-white/50 dark:bg-[#0a0e1a]/50 border border-slate-200 dark:border-white/10 text-slate-800 dark:text-white rounded-2xl pl-12 pr-4 py-4 font-bold focus:outline-none focus:ring-4 focus:ring-indigo-500/20 dark:focus:ring-indigo-500/30 focus:border-indigo-500 dark:focus:border-indigo-400 transition-all shadow-inner dark:shadow-none placeholder:font-normal placeholder:text-slate-400 dark:placeholder:text-slate-600 tracking-widest"
                              placeholder="••••••••">
                    </div>
                 </div>
              </div>

              <!-- Submit Button -->
              <button type="submit" :disabled="isLoading"
                      class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-indigo-500 dark:to-purple-600 text-white font-black text-lg py-4 rounded-2xl shadow-[0_10px_30px_rgba(79,70,229,0.3)] dark:shadow-[0_10px_30px_rgba(99,102,241,0.2)] hover:shadow-[0_10px_40px_rgba(79,70,229,0.4)] disabled:opacity-70 disabled:cursor-not-allowed transition-all hover:-translate-y-1 active:translate-y-0 mt-8 flex items-center justify-center gap-3 relative overflow-hidden group/btn">
                 <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/30 to-transparent -translate-x-[150%] skew-x-[-20deg] group-hover/btn:translate-x-[150%] transition-transform duration-1000"></div>
                 
                 <svg v-if="isLoading" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                 <svg v-else class="w-5 h-5 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                 
                 <span>{{ isLoading ? 'MENGOTENTIKASI...' : 'MASUK SISTEM' }}</span>
              </button>
           </form>
           
           <div class="mt-10 pt-6 border-t border-slate-100 dark:border-white/5 text-center flex flex-col items-center justify-center gap-2 relative z-10 transition-colors">
              <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest bg-slate-100 dark:bg-white/5 px-3 py-1 rounded-full transition-colors">IoT Secure Authentication</span>
              <p class="text-xs font-semibold text-slate-400 dark:text-slate-600 transition-colors">Dilindungi enkripsi end-to-end</p>
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
