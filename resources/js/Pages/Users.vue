<template>
  <div class="max-w-6xl mx-auto space-y-8 pb-12">
     <!-- Header -->
     <div class="bg-white/60 dark:bg-white/[0.04] backdrop-blur-xl p-8 rounded-[2rem] border border-white dark:border-white/10 shadow-sm dark:shadow-none flex flex-col md:flex-row gap-6 justify-between items-center relative overflow-hidden transition-colors duration-500">
        <div class="absolute right-0 top-0 w-64 h-64 bg-emerald-100/40 dark:bg-emerald-500/10 rounded-full blur-[60px] pointer-events-none -mr-10 -mt-10 transition-colors"></div>
        <div class="relative z-10 text-center md:text-left flex-1">
           <h3 class="text-3xl md:text-4xl font-black text-slate-800 dark:text-slate-100 tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 to-teal-600 dark:from-emerald-400 dark:to-teal-300 transition-colors">Akses Keluarga</h3>
           <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium text-lg max-w-xl transition-colors">Kelola siapa saja anggota keluarga atau administrator sekunder yang boleh mengendalikan dashboard IoT ini.</p>
        </div>
        
        <button v-if="currentUser?.role === 'admin'" @click="openModal()" class="relative z-10 bg-gradient-to-r from-emerald-500 to-teal-500 text-white px-6 py-4 rounded-2xl font-black shadow-[0_10px_25px_rgba(16,185,129,0.3)] hover:shadow-[0_10px_35px_rgba(16,185,129,0.4)] transition-all hover:scale-[1.02] active:scale-95 flex items-center gap-3 w-full md:w-auto justify-center group overflow-hidden">
           <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-[150%] skew-x-[-20deg] group-hover:translate-x-[150%] transition-transform duration-700"></div>
           <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
           TAMBAH ANGGOTA
        </button>
     </div>

     <!-- Loading Skeleton -->
     <div v-if="isLoading && users.length === 0" class="animate-pulse space-y-4">
        <div v-for="i in 3" :key="i" class="bg-white/50 dark:bg-white/5 rounded-[1.5rem] h-24 transition-colors"></div>
     </div>

     <!-- Data Table or List -->
     <div v-else class="bg-white/70 dark:bg-white/[0.04] backdrop-blur-xl rounded-[2rem] border border-white dark:border-white/10 shadow-sm dark:shadow-none overflow-hidden transition-colors duration-500">
        <div v-if="users.length === 0" class="p-12 text-center">
            <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800/50 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl text-slate-300 dark:text-slate-600 transition-colors">👥</div>
            <h4 class="text-xl font-bold text-slate-700 dark:text-slate-200 transition-colors">Belum ada anggota lain</h4>
            <p class="text-slate-500 dark:text-slate-400 mt-2 transition-colors">Daftarkan keluarga Anda agar mereka juga bisa mengontrol jemuran.</p>
        </div>
        
        <div v-else class="divide-y divide-slate-100/50 dark:divide-white/5 transition-colors">
           <div v-for="user in users" :key="user.id" class="p-6 md:p-8 flex flex-col md:flex-row items-center gap-6 hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors group">
              
              <div class="w-16 h-16 bg-gradient-to-br from-indigo-100 to-blue-100 dark:from-indigo-500/20 dark:to-blue-500/20 rounded-[1.25rem] flex items-center justify-center text-xl font-black text-blue-600 dark:text-indigo-400 shadow-inner ring-4 ring-white dark:ring-slate-900 border border-blue-50/50 dark:border-indigo-500/30 uppercase flex-shrink-0 group-hover:scale-110 transition-all">
                 {{ user.name.charAt(0) }}
              </div>
              
              <div class="flex-1 text-center md:text-left">
                 <div class="flex items-center justify-center md:justify-start gap-3">
                    <h4 class="text-xl font-extrabold text-slate-800 dark:text-slate-100 transition-colors">{{ user.name }}</h4>
                 </div>
                 <p class="text-sm font-bold text-slate-400 dark:text-slate-500 mt-1 flex items-center justify-center md:justify-start gap-1 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    {{ user.email }}
                 </p>
                 <p class="text-[10px] font-black tracking-widest text-emerald-500 dark:text-emerald-400 uppercase mt-2 bg-emerald-50 dark:bg-emerald-500/10 w-max mx-auto md:mx-0 px-2.5 py-1 rounded-md border border-emerald-100/50 dark:border-emerald-500/20 shadow-sm dark:shadow-none transition-colors" title="Member Sejak">
                    Terdaftar: {{ new Date(user.created_at).toLocaleDateString() }}
                 </p>
                 <p v-if="user.phone" class="text-[10px] font-black tracking-widest text-blue-500 dark:text-blue-400 uppercase mt-2 bg-blue-50 dark:bg-blue-500/10 w-max mx-auto md:mx-0 px-2.5 py-1 rounded-md border border-blue-100/50 dark:border-blue-500/20 shadow-sm dark:shadow-none transition-colors" title="Nomor WhatsApp">
                    WA: {{ user.phone }}
                 </p>
              </div>

              <div class="flex gap-3 w-full md:w-auto">
                 <button v-if="currentUser?.role === 'admin' || currentUser?.id === user.id" @click="openModal(user)" class="flex-1 md:flex-none px-5 py-3 bg-white dark:bg-slate-800 text-blue-600 dark:text-indigo-400 border border-slate-200 dark:border-slate-700 shadow-sm dark:shadow-none rounded-xl font-bold hover:bg-blue-50 dark:hover:bg-slate-700 hover:border-blue-200 dark:hover:border-slate-600 transition-colors">Edit</button>
                 <button v-if="currentUser?.role === 'admin' || currentUser?.id === user.id" @click="deleteUser(user.id)" class="flex-1 md:flex-none px-5 py-3 bg-white dark:bg-slate-800 text-rose-600 dark:text-rose-400 border border-slate-200 dark:border-slate-700 shadow-sm dark:shadow-none rounded-xl font-bold hover:bg-rose-50 dark:hover:bg-slate-700 hover:border-rose-200 dark:hover:border-slate-600 transition-colors">Hapus</button>
              </div>
           </div>
        </div>
     </div>

     <!-- Modal Overlay (Glassmorphism) -->
     <transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100" leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
        <div v-if="isModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
           
           <div class="absolute inset-0 bg-slate-900/40 dark:bg-black/60 backdrop-blur-sm transition-colors" @click="closeModal"></div>
           
           <div class="bg-white/90 dark:bg-[#0f172a]/95 backdrop-blur-2xl rounded-[2.5rem] w-full max-w-lg relative z-10 shadow-2xl border border-white dark:border-white/10 overflow-hidden p-8 md:p-10 transform transition-all">
              <div class="absolute top-0 right-0 w-32 h-32 bg-blue-100 dark:bg-indigo-500/20 opacity-50 rounded-full -mr-10 -mt-10 blur-2xl transition-colors"></div>
              
              <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-8 relative z-10 transition-colors">{{ form.id ? 'Edit Data Anggota' : 'Daftarkan Anggota Baru' }}</h3>
              
              <form @submit.prevent="saveUser" class="space-y-5 relative z-10">
                 
                 <div v-if="errorMessage" class="bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 px-4 py-3 rounded-xl text-sm font-bold border border-rose-100 dark:border-rose-800/50 transition-colors">{{ errorMessage }}</div>

                 <div class="space-y-1.5 relative">
                    <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest pl-2 transition-colors">Nama Lengkap</label>
                    <input type="text" v-model="form.name" required placeholder="Mis. Istri Tersayang" class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl px-5 py-3.5 font-bold focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:focus:border-indigo-400 transition-all">
                 </div>

                 <div class="space-y-1.5 relative">
                    <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest pl-2 transition-colors">Alamat Email (Untuk Login)</label>
                    <input type="email" v-model="form.email" required placeholder="keluarga@iot.com" class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl px-5 py-3.5 font-bold focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:focus:border-indigo-400 transition-all">
                 </div>

                 <div class="space-y-1.5 relative">
                    <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest pl-2 transition-colors">Nomor WhatsApp (Opsional)</label>
                    <input type="text" v-model="form.phone" placeholder="Mis. 628123456789 (Gunakan format 62 atau 08)" class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl px-5 py-3.5 font-bold focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:focus:border-indigo-400 transition-all">
                 </div>

                 <div class="space-y-1.5 relative">
                    <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest pl-2 transition-colors">Sandi Rahasia {{ form.id ? '(Kosongkan jika tidak diubah)' : '' }}</label>
                    <div class="relative flex items-center">
                       <input :type="showPassword ? 'text' : 'password'" v-model="form.password" :required="!form.id" placeholder="Minimal 4 karakter" class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl px-5 py-3.5 pr-12 font-bold focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:focus:border-indigo-400 transition-all">
                       <button type="button" @click="showPassword = !showPassword" class="absolute right-4 text-slate-400 hover:text-blue-500 dark:hover:text-indigo-400 transition-colors focus:outline-none" tabindex="-1">
                          <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                          <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                       </button>
                    </div>
                 </div>

                 <div class="pt-6 flex gap-3">
                    <button type="button" @click="closeModal" class="flex-1 py-4 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded-xl font-black hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">BATAL</button>
                    <button type="submit" :disabled="isSaving" class="flex-1 flex justify-center items-center py-4 bg-blue-600 text-white rounded-xl shadow-[0_5px_15px_rgba(37,99,235,0.3)] hover:shadow-[0_5px_20px_rgba(37,99,235,0.4)] font-black transition-all hover:-translate-y-0.5 disabled:opacity-70 disabled:cursor-not-allowed">
                       <svg v-if="isSaving" class="w-5 h-5 animate-spin mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                       {{ isSaving ? 'MEMPROSES...' : 'SIMPAN' }}
                    </button>
                 </div>
              </form>
           </div>
        </div>
     </transition>

  </div>
</template>

<script>
import axios from 'axios';

export default {
  emits: ['toast'],
  props: {
    isDarkMode: { type: Boolean, default: true },
    currentUser: { type: Object, default: () => ({}) }
  },
  data() {
    return {
      users: [],
      isLoading: true,
      isModalOpen: false,
      isSaving: false,
      showPassword: false,
      errorMessage: '',
      form: {
        id: null,
        name: '',
        email: '',
        password: '',
        phone: ''
      }
    }
  },
  mounted() {
    this.fetchUsers();
  },
  methods: {
    async fetchUsers() {
      this.isLoading = true;
      try {
         const response = await axios.get('/api/users');
         this.users = response.data;
      } catch (error) {
         console.error(error);
         this.$emit('toast', { type: 'error', title: 'Gagal', message: 'Tidak dapat memuat data pengguna.' });
      } finally {
         this.isLoading = false;
      }
    },
    openModal(user = null) {
       this.errorMessage = '';
       this.showPassword = false;
       if (user) {
          this.form = { id: user.id, name: user.name, email: user.email, password: '', phone: user.phone || '' };
       } else {
          this.form = { id: null, name: '', email: '', password: '', phone: '' };
       }
       this.isModalOpen = true;
    },
    closeModal() {
       this.isModalOpen = false;
    },
    async saveUser() {
       this.isSaving = true;
       this.errorMessage = '';
       
       try {
          if (this.form.id) {
             await axios.put(`/api/users/${this.form.id}`, this.form);
             this.$emit('toast', { type: 'success', title: 'Diperbarui', message: 'Data pengguna berhasil diubah.' });
          } else {
             await axios.post('/api/users', this.form);
             this.$emit('toast', { type: 'success', title: 'Berhasil', message: 'Anggota keluarga baru ditambahkan!' });
          }
          this.closeModal();
          this.fetchUsers();
       } catch (error) {
          if (error.response && error.response.data && error.response.data.message) {
             this.errorMessage = error.response.data.message; // validation errors from backend usually
          } else {
             this.errorMessage = 'Terjadi kesalahan sistem.';
          }
       } finally {
          this.isSaving = false;
       }
    },
    async deleteUser(id) {
       if (!confirm('Anda yakin ingin menghapus izin akses anggota ini secara permanen?')) return;
       
       try {
          await axios.delete(`/api/users/${id}`);
          this.$emit('toast', { type: 'success', title: 'Terhapus', message: 'Akses pengguna telah dicabut.' });
          this.fetchUsers();
       } catch (error) {
          if (error.response && error.response.data && error.response.data.error) {
             this.$emit('toast', { type: 'error', title: 'Dilarang', message: error.response.data.error });
          } else {
             this.$emit('toast', { type: 'error', title: 'Gagal', message: 'Tidak dapat menghapus data.' });
          }
       }
    }
  }
}
</script>
