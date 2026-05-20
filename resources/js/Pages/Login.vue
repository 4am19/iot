<template>
  <div class="login-root" ref="loginRoot">
    <canvas ref="canvas" class="login-canvas"></canvas>
    <div class="login-orb login-orb--1"></div>
    <div class="login-orb login-orb--2"></div>
    <div class="login-orb login-orb--3"></div>

    <!-- Theme Toggle Button -->
    <button class="theme-toggle" @click="toggleTheme" :title="isDarkMode ? 'Mode Terang' : 'Mode Gelap'">
      <div class="theme-toggle-icon" :class="{ 'is-dark': isDarkMode }">
        <svg v-if="isDarkMode" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        <svg v-else width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
      </div>
    </button>

    <div class="login-container">
      <!-- Left: Branding -->
      <div class="login-brand">
        <div class="brand-content">
          <div class="brand-icon">
            <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
          </div>
          <h1 class="brand-title">IoT Jemuran<br><span>Pintar</span></h1>
          <p class="brand-desc">Sistem otomatisasi jemuran berbasis ESP32 dengan monitoring cuaca real-time dan kontrol cerdas.</p>
          <div class="brand-features">
            <div class="feature-item"><div class="feature-dot feature-dot--cyan"></div><span>Deteksi Panas Otomatis</span></div>
            <div class="feature-item"><div class="feature-dot feature-dot--green"></div><span>Deteksi Hujan Otomatis</span></div>
            <div class="feature-item"><div class="feature-dot feature-dot--purple"></div><span>Kontrol Motor Servo</span></div>
            <div class="feature-item"><div class="feature-dot feature-dot--amber"></div><span>Notifikasi Real-time</span></div>
          </div>
        </div>
        <p class="brand-footer">© 2026 IoT Smart Clothesline</p>
      </div>

      <!-- Right: Login Form -->
      <div class="login-form-side">
        <div class="login-card">
          <div class="card-glow"></div>
          <div class="card-inner">
            <div class="card-header">
              <div class="card-logo">
                <svg width="28" height="28" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/></svg>
              </div>
              <h2>Masuk Sistem</h2>
              <p>Akses dashboard kontrol IoT Anda</p>
            </div>

            <transition enter-active-class="err-enter-active" enter-from-class="err-enter-from" enter-to-class="err-enter-to" leave-active-class="err-leave-active" leave-from-class="err-enter-to" leave-to-class="err-enter-from">
              <div v-if="errorMessage" class="login-error">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ errorMessage }}</span>
              </div>
            </transition>

            <form @submit.prevent="login" class="login-form">
              <div class="input-group" :class="{ focused: focusEmail }">
                <label>Email</label>
                <div class="input-wrap">
                  <svg class="input-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                  <input type="email" v-model="form.email" required placeholder="admin@iot.com" @focus="focusEmail=true" @blur="focusEmail=false">
                </div>
              </div>

              <div class="input-group" :class="{ focused: focusPass }">
                <label>Kata Sandi</label>
                <div class="input-wrap">
                  <svg class="input-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                  <input :type="showPass ? 'text' : 'password'" v-model="form.password" required placeholder="••••••••" @focus="focusPass=true" @blur="focusPass=false">
                  <button type="button" class="pass-toggle" @click="showPass=!showPass" tabindex="-1">
                    <svg v-if="!showPass" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg v-else width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                  </button>
                </div>
              </div>

              <button type="submit" class="login-btn" :disabled="isLoading">
                <div class="btn-shimmer"></div>
                <svg v-if="isLoading" class="spin" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <svg v-else width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                <span>{{ isLoading ? 'Mengautentikasi...' : 'Masuk Sistem' }}</span>
              </button>
            </form>

            <div class="card-footer">
              <div class="footer-badge">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Enkripsi End-to-End</span>
              </div>
            </div>
          </div>
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
      form: { email: '', password: '' },
      isLoading: false,
      errorMessage: '',
      focusEmail: false,
      focusPass: false,
      showPass: false,
      isDarkMode: true,
      particles: [],
      animFrame: null
    }
  },
  mounted() {
    axios.get('/sanctum/csrf-cookie').catch(() => {});
    // Load saved theme or detect system preference
    const saved = localStorage.getItem('iot_theme');
    if (saved) {
      this.isDarkMode = saved === 'dark';
    } else {
      this.isDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
    }
    this.applyTheme();
    
    // Mouse tracking for particles
    this.mouseX = -1000;
    this.mouseY = -1000;
    this.handleMouseMove = (e) => {
      this.mouseX = e.clientX;
      this.mouseY = e.clientY;
    };
    this.handleMouseOut = () => {
      this.mouseX = -1000;
      this.mouseY = -1000;
    };
    window.addEventListener('mousemove', this.handleMouseMove);
    window.addEventListener('mouseout', this.handleMouseOut);

    this.initCanvas();
    window.addEventListener('resize', this.resizeCanvas);
  },
  beforeUnmount() {
    cancelAnimationFrame(this.animFrame);
    window.removeEventListener('resize', this.resizeCanvas);
    window.removeEventListener('mousemove', this.handleMouseMove);
    window.removeEventListener('mouseout', this.handleMouseOut);
  },
  methods: {
    initCanvas() {
      const c = this.$refs.canvas;
      if (!c) return;
      const ctx = c.getContext('2d');
      this.resizeCanvas();
      const count = Math.min(160, Math.floor(window.innerWidth / 10));
      this.particles = Array.from({ length: count }, () => ({
        x: Math.random() * c.width,
        y: Math.random() * c.height,
        r: Math.random() * 2 + 1,
        dx: (Math.random() - 0.5) * 0.8,
        dy: (Math.random() - 0.5) * 0.8,
        o: Math.random() * 0.6 + 0.2
      }));
      const draw = () => {
        ctx.clearRect(0, 0, c.width, c.height);
        const dark = document.documentElement.classList.contains('dark');
        const dotColor = dark ? '129,140,248' : '67,56,202';
        const dotOpacityMul = dark ? 1 : 1.2;
        const lineOpacityMul = dark ? 0.1 : 0.15;
        this.particles.forEach((p, i) => {
          // Interact with mouse
          const dxMouse = this.mouseX - p.x;
          const dyMouse = this.mouseY - p.y;
          const distMouse = Math.hypot(dxMouse, dyMouse);
          
          if (distMouse < 180) {
            const angle = Math.atan2(dyMouse, dxMouse);
            const force = (180 - distMouse) / 180;
            p.x -= Math.cos(angle) * force * 2;
            p.y -= Math.sin(angle) * force * 2;
          }

          p.x += p.dx; p.y += p.dy;
          if (p.x < 0) p.x = c.width;
          if (p.x > c.width) p.x = 0;
          if (p.y < 0) p.y = c.height;
          if (p.y > c.height) p.y = 0;

          ctx.beginPath();
          ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
          ctx.fillStyle = `rgba(${dotColor},${Math.min(1, p.o * dotOpacityMul)})`;
          ctx.fill();

          // Draw line to mouse
          if (distMouse < 180) {
            ctx.beginPath();
            ctx.moveTo(p.x, p.y);
            ctx.lineTo(this.mouseX, this.mouseY);
            ctx.strokeStyle = `rgba(${dotColor},${lineOpacityMul * 3 * (1 - distMouse / 180)})`;
            ctx.lineWidth = 1;
            ctx.stroke();
          }

          for (let j = i + 1; j < this.particles.length; j++) {
            const p2 = this.particles[j];
            const dist = Math.hypot(p.x - p2.x, p.y - p2.y);
            if (dist < 130) {
              ctx.beginPath();
              ctx.moveTo(p.x, p.y);
              ctx.lineTo(p2.x, p2.y);
              ctx.strokeStyle = `rgba(${dotColor},${lineOpacityMul * (1 - dist / 130)})`;
              ctx.lineWidth = 0.6;
              ctx.stroke();
            }
          }
        });
        this.animFrame = requestAnimationFrame(draw);
      };
      draw();
    },
    resizeCanvas() {
      const c = this.$refs.canvas;
      if (!c) return;
      c.width = window.innerWidth;
      c.height = window.innerHeight;
    },
    async login() {
      this.isLoading = true;
      this.errorMessage = '';
      try {
        await axios.get('/sanctum/csrf-cookie');
        const response = await axios.post('/api/auth/login', this.form);
        if (response.data.success) {
          this.$emit('login-success', response.data.user);
        }
      } catch (error) {
        if (error.response && error.response.status === 401) {
          this.errorMessage = 'Email atau kata sandi salah!';
        } else {
          this.errorMessage = 'Gagal terhubung ke server.';
        }
      } finally {
        this.isLoading = false;
      }
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
    }
  }
}
</script>

<style>
/* ===== BASE (Light Mode) ===== */
.login-root {
  min-height: 100vh;
  background: #f0f4f8;
  color: #1e293b;
  position: relative;
  overflow: hidden;
  font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
  transition: background 0.5s, color 0.5s;
}
.dark .login-root { background: #050816; color: #f1f5f9; }

.login-canvas { position: fixed; inset: 0; z-index: 0; pointer-events: none; }

/* Orbs */
.login-orb { position: absolute; border-radius: 50%; filter: blur(80px); pointer-events: none; z-index: 0; transition: background 0.5s; }
.login-orb--1 { width: 800px; height: 800px; top: -10%; left: -10%; background: radial-gradient(circle, rgba(99,102,241,0.4), transparent 60%); animation: orbFloat 18s ease-in-out infinite; }
.login-orb--2 { width: 700px; height: 700px; bottom: -10%; right: -10%; background: radial-gradient(circle, rgba(168,85,247,0.3), transparent 60%); animation: orbFloat 22s ease-in-out infinite reverse; }
.login-orb--3 { width: 600px; height: 600px; top: 20%; left: 40%; background: radial-gradient(circle, rgba(6,182,212,0.25), transparent 60%); animation: orbFloat 15s ease-in-out infinite 3s; }
.dark .login-orb--1 { background: radial-gradient(circle, rgba(99,102,241,0.25), transparent 60%); }
.dark .login-orb--2 { background: radial-gradient(circle, rgba(168,85,247,0.2), transparent 60%); }
.dark .login-orb--3 { background: radial-gradient(circle, rgba(6,182,212,0.12), transparent 60%); }
@keyframes orbFloat {
  0%, 100% { transform: translate(0, 0) scale(1); }
  33% { transform: translate(30px, -40px) scale(1.1); }
  66% { transform: translate(-20px, 20px) scale(0.95); }
}

/* Layout */
.login-container { 
  position: relative; z-index: 1; min-height: 100vh; 
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  padding: 2rem;
  gap: 2rem;
  perspective: 1000px;
}

@keyframes floatDown {
  0% { opacity: 0; transform: translateY(-40px); }
  100% { opacity: 1; transform: translateY(0); }
}
@keyframes floatUp {
  0% { opacity: 0; transform: translateY(40px); }
  100% { opacity: 1; transform: translateY(0); }
}

/* Branding Header */
.login-brand { 
  display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative; 
  animation: floatDown 1s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
}
.brand-content { 
  display: flex; flex-direction: column; align-items: center; text-align: center;
}
.brand-icon {
  width: 72px; height: 72px; border-radius: 22px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  display: flex; align-items: center; justify-content: center;
  color: white; box-shadow: 0 8px 32px rgba(99,102,241,0.35);
  margin-bottom: 1.5rem; animation: iconPulse 3s ease-in-out infinite;
}
.brand-icon svg { width: 32px; height: 32px; }
@keyframes iconPulse {
  0%, 100% { box-shadow: 0 8px 32px rgba(99,102,241,0.35); }
  50% { box-shadow: 0 8px 48px rgba(99,102,241,0.5); }
}
.brand-title { font-size: 2.8rem; font-weight: 800; color: #1e293b; line-height: 1.1; margin: 0; letter-spacing: -0.03em; transition: color 0.5s; }
.dark .brand-title { color: #f1f5f9; }
.brand-title br { display: none; }
.brand-title span { background: linear-gradient(135deg, #6366f1, #a855f7); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-left: 12px; }
.dark .brand-title span { background: linear-gradient(135deg, #818cf8, #c084fc); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

/* Hide redundant elements to keep it simple */
.brand-desc, .brand-features, .brand-footer { display: none !important; }

/* Right Form */
.login-form-side { 
  display: flex; align-items: center; justify-content: center; width: 100%; 
  opacity: 0;
  animation: floatUp 1s cubic-bezier(0.2, 0.8, 0.2, 1) 0.15s forwards;
}
.login-card { width: 100%; max-width: 420px; position: relative; }
.card-glow {
  position: absolute; inset: -1px; border-radius: 28px;
  background: linear-gradient(135deg, rgba(99,102,241,0.4), rgba(168,85,247,0.2), rgba(6,182,212,0.2));
  z-index: 0; opacity: 0; transition: opacity 0.5s;
}
.login-card:hover .card-glow { opacity: 1; }
.card-inner {
  position: relative; z-index: 1;
  background: rgba(255,255,255,0.85);
  backdrop-filter: blur(40px) saturate(150%);
  border: 1px solid rgba(0,0,0,0.06);
  border-radius: 28px; padding: 2.5rem;
  box-shadow: 0 20px 60px rgba(0,0,0,0.06);
  transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.login-card:hover .card-inner {
  transform: translateY(-6px);
  box-shadow: 0 30px 70px rgba(0,0,0,0.1);
  border-color: rgba(99,102,241,0.15);
}
.dark .card-inner {
  background: rgba(15,23,42,0.75);
  border: 1px solid rgba(255,255,255,0.08);
  box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}
.dark .login-card:hover .card-inner {
  box-shadow: 0 30px 70px rgba(0,0,0,0.5);
  border-color: rgba(99,102,241,0.25);
}

/* Card Header */
.card-header { text-align: center; margin-bottom: 2rem; }
.card-logo { display: none !important; }
.card-header h2 { font-size: 1.6rem; font-weight: 800; color: #1e293b; margin: 0 0 0.4rem 0; letter-spacing: -0.02em; transition: color 0.5s; }
.dark .card-header h2 { color: #f1f5f9; }
.card-header p { color: #94a3b8; font-size: 0.85rem; margin: 0; font-weight: 500; transition: color 0.5s; }
.dark .card-header p { color: #64748b; }

/* Error */
.login-error {
  display: flex; align-items: center; gap: 10px; padding: 12px 16px;
  background: rgba(244,63,94,0.08); border: 1px solid rgba(244,63,94,0.15);
  border-radius: 14px; color: #e11d48; font-size: 0.85rem; font-weight: 600; margin-bottom: 1.25rem;
  transition: background 0.5s, color 0.5s, border-color 0.5s;
}
.dark .login-error { background: rgba(244,63,94,0.1); border-color: rgba(244,63,94,0.2); color: #fb7185; }
.err-enter-active { transition: all 0.3s ease-out; }
.err-leave-active { transition: all 0.2s ease-in; }
.err-enter-from { opacity: 0; transform: translateY(-8px); }
.err-enter-to { opacity: 1; transform: translateY(0); }

/* Form */
.login-form { display: flex; flex-direction: column; gap: 1.25rem; }
.input-group label {
  display: block; font-size: 0.7rem; font-weight: 700;
  text-transform: uppercase; letter-spacing: 0.1em;
  color: #94a3b8; margin-bottom: 8px; padding-left: 4px; transition: color 0.3s;
}
.dark .input-group label { color: #64748b; }
.input-group.focused label { color: #6366f1; }
.dark .input-group.focused label { color: #818cf8; }
.input-wrap { position: relative; display: flex; align-items: center; }
.input-icon { position: absolute; left: 16px; color: #94a3b8; transition: color 0.3s; pointer-events: none; flex-shrink: 0; }
.dark .input-icon { color: #475569; }
.input-group.focused .input-icon { color: #6366f1; }
.dark .input-group.focused .input-icon { color: #818cf8; }
.input-wrap input {
  width: 100%;
  background: rgba(241,245,249,0.8);
  border: 1px solid rgba(0,0,0,0.08);
  border-radius: 14px; padding: 14px 16px 14px 46px;
  color: #1e293b; font-size: 0.9rem; font-weight: 600;
  font-family: inherit; outline: none; transition: all 0.3s;
}
.dark .input-wrap input {
  background: rgba(15,23,42,0.6);
  border: 1px solid rgba(255,255,255,0.08);
  color: #f1f5f9;
}
.input-wrap input:-webkit-autofill,
.input-wrap input:-webkit-autofill:hover, 
.input-wrap input:-webkit-autofill:focus, 
.input-wrap input:-webkit-autofill:active{
    -webkit-box-shadow: 0 0 0 30px #f8fafc inset !important;
    -webkit-text-fill-color: #1e293b !important;
    border-radius: 14px;
}
.dark .input-wrap input:-webkit-autofill,
.dark .input-wrap input:-webkit-autofill:hover, 
.dark .input-wrap input:-webkit-autofill:focus, 
.dark .input-wrap input:-webkit-autofill:active{
    -webkit-box-shadow: 0 0 0 30px #0f172a inset !important;
    -webkit-text-fill-color: #f1f5f9 !important;
}
.input-wrap input::placeholder { color: #94a3b8; font-weight: 400; }
.dark .input-wrap input::placeholder { color: #334155; }
.input-wrap input:focus {
  border-color: rgba(99,102,241,0.5);
  box-shadow: 0 0 0 4px rgba(99,102,241,0.08), 0 0 20px rgba(99,102,241,0.03);
  background: rgba(255,255,255,0.95);
}
.dark .input-wrap input:focus {
  box-shadow: 0 0 0 4px rgba(99,102,241,0.1), 0 0 20px rgba(99,102,241,0.05);
  background: rgba(15,23,42,0.8);
}
.pass-toggle {
  position: absolute; right: 14px; background: none; border: none;
  color: #94a3b8; cursor: pointer; padding: 4px; display: flex; transition: color 0.2s;
}
.dark .pass-toggle { color: #475569; }
.pass-toggle:hover { color: #6366f1; }
.dark .pass-toggle:hover { color: #818cf8; }

/* Submit */
.login-btn {
  display: flex; align-items: center; justify-content: center; gap: 10px;
  width: 100%; padding: 16px; margin-top: 0.5rem; border: none; border-radius: 16px;
  background: linear-gradient(135deg, #6366f1, #7c3aed);
  color: white; font-size: 0.95rem; font-weight: 700; font-family: inherit;
  cursor: pointer; position: relative; overflow: hidden; transition: all 0.3s;
  box-shadow: 0 8px 32px rgba(99,102,241,0.3);
}
.login-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 12px 40px rgba(99,102,241,0.45); }
.login-btn:active:not(:disabled) { transform: translateY(0); }
.login-btn:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-shimmer {
  position: absolute; inset: 0;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
  transform: translateX(-100%) skewX(-20deg);
}
.login-btn:hover .btn-shimmer { animation: shimmer 0.8s ease forwards; }
@keyframes shimmer { to { transform: translateX(100%) skewX(-20deg); } }
.spin { animation: spin 1s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* Footer */
.card-footer { text-align: center; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(0,0,0,0.05); transition: border-color 0.5s; }
.dark .card-footer { border-top-color: rgba(255,255,255,0.05); }
.footer-badge {
  display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px;
  background: rgba(0,0,0,0.03); border-radius: 100px;
  font-size: 0.7rem; font-weight: 700; color: #94a3b8;
  letter-spacing: 0.04em; text-transform: uppercase; transition: background 0.5s, color 0.5s;
}
.dark .footer-badge { background: rgba(255,255,255,0.04); color: #475569; }

/* Theme Toggle */
.theme-toggle {
  position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 50;
  width: 44px; height: 44px; border-radius: 14px;
  border: 1px solid rgba(0,0,0,0.08);
  background: rgba(255,255,255,0.8);
  backdrop-filter: blur(20px);
  display: flex; align-items: center; justify-content: center;
  cursor: pointer;
  box-shadow: 0 4px 16px rgba(0,0,0,0.06);
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.theme-toggle:hover {
  transform: translateY(-4px) scale(1.05);
  box-shadow: 0 10px 25px rgba(99,102,241,0.2);
  border-color: rgba(99,102,241,0.3);
}
.dark .theme-toggle {
  background: rgba(30,41,59,0.7);
  border-color: rgba(255,255,255,0.1);
  box-shadow: 0 4px 16px rgba(0,0,0,0.3);
}
.theme-toggle:hover {
  transform: scale(1.1);
  box-shadow: 0 6px 24px rgba(99,102,241,0.2);
}
.theme-toggle:active { transform: scale(0.95); }
.theme-toggle-icon {
  display: flex; align-items: center; justify-content: center;
  color: #6366f1;
  transition: transform 0.5s cubic-bezier(0.34,1.56,0.64,1), color 0.3s;
}
.theme-toggle-icon.is-dark {
  color: #fbbf24;
  filter: drop-shadow(0 0 8px rgba(251,191,36,0.5));
}
.theme-toggle:hover .theme-toggle-icon { transform: rotate(30deg); }

/* Mobile Responsive Adjustments */
@media (max-width: 768px) {
  .login-container { 
    padding: 1rem;
    gap: 1.5rem;
    justify-content: flex-start;
  }
  .login-brand { 
    padding: 2.5rem 1rem 0; 
  }
  .brand-content {
    flex-direction: row;
    gap: 1rem;
  }
  .brand-icon { 
    width: 48px; height: 48px; 
    margin-bottom: 0; 
    border-radius: 14px;
  }
  .brand-icon svg { width: 24px; height: 24px; }
  .brand-title { 
    font-size: 1.8rem; 
  }
  .card-inner { padding: 1.8rem; }
}
</style>
