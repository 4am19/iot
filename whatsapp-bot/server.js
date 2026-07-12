const express = require('express');
const cors = require('cors');
const {
    default: makeWASocket,
    useMultiFileAuthState,
    DisconnectReason,
    fetchLatestBaileysVersion
} = require('@whiskeysockets/baileys');
const pino = require('pino');
const qrcode = require('qrcode-terminal');
const path = require('path');

const app = express();
app.use(cors());
app.use(express.json());

const PORT = 3000;
let sock = null;
let isConnected = false;

// Format phone number
const formatPhone = (phone) => {
    let formatted = phone.replace(/\D/g, '');
    if (formatted.startsWith('08')) formatted = '628' + formatted.substring(2);
    else if (formatted.startsWith('8')) formatted = '628' + formatted.substring(1);
    if (!formatted.endsWith('@s.whatsapp.net')) formatted += '@s.whatsapp.net';
    return formatted;
};

// Initialize WhatsApp connection
async function connectToWhatsApp() {
    const authPath = path.join(__dirname, 'auth_info_baileys');
    const { state, saveCreds } = await useMultiFileAuthState(authPath);
    const { version } = await fetchLatestBaileysVersion();

    sock = makeWASocket({
        version,
        auth: state,
        printQRInTerminal: false,
        logger: pino({ level: 'silent' }),
        browser: ['Windows', 'Chrome', '10.0.0'], // Menyamar sebagai Chrome di Windows
    });

    sock.ev.on('connection.update', (update) => {
        const { connection, lastDisconnect, qr } = update;

        if (qr) {
            console.log('\n[!] SILAKAN SCAN QR CODE DI BAWAH INI MENGGUNAKAN WHATSAPP ANDA:\n');
            qrcode.generate(qr, { small: true });
        }

        if (connection === 'close') {
            isConnected = false;
            const shouldReconnect = lastDisconnect.error?.output?.statusCode !== DisconnectReason.loggedOut;
            console.log('\nKoneksi terputus. Alasan:', lastDisconnect.error?.message || 'Unknown');
            if (shouldReconnect) {
                console.log('Mencoba menyambung kembali dalam 5 detik...');
                setTimeout(connectToWhatsApp, 5000);
            } else {
                console.log('Sesi logout. Hapus folder "auth_info_baileys" dan jalankan ulang untuk scan baru.');
            }
        } else if (connection === 'open') {
            isConnected = true;
            console.log('\n✅ WHATSAPP BOT BERHASIL TERHUBUNG!\n');
            console.log(`Menjalankan API server pada http://localhost:${PORT}`);
        }
    });

    sock.ev.on('creds.update', saveCreds);
}

app.post('/send-broadcast', async (req, res) => {
    if (!isConnected || !sock) return res.status(503).json({ success: false, message: 'WhatsApp bot is not connected yet.' });

    const { numbers, message } = req.body;
    if (!numbers || !Array.isArray(numbers) || numbers.length === 0) return res.status(400).json({ success: false, message: 'Numbers array is required and cannot be empty.' });
    if (!message) return res.status(400).json({ success: false, message: 'Message text is required.' });

    const results = [];
    for (const phone of numbers) {
        if (!phone || phone.trim() === '') continue;
        const formattedJid = formatPhone(phone);
        try {
            const [result] = await sock.onWhatsApp(formattedJid);
            if (result && result.exists) {
                await sock.sendMessage(result.jid, { text: message });
                results.push({ phone, status: 'success' });
                console.log(`[Berhasil] Pesan terkirim ke: ${phone}`);
            } else {
                results.push({ phone, status: 'failed', reason: 'Not registered on WhatsApp' });
                console.log(`[Gagal] Nomor tidak terdaftar di WA: ${phone}`);
            }
        } catch (error) {
            results.push({ phone, status: 'error', reason: error.message });
            console.error(`[Error] Gagal mengirim ke ${phone}:`, error.message);
        }
        await new Promise(resolve => setTimeout(resolve, 2000));
    }
    res.json({ success: true, message: 'Broadcast completed', results });
});

app.listen(PORT, () => {
    console.log(`\n==============================================`);
    console.log(`🚀 Node.js WhatsApp Microservice`);
    console.log(`==============================================`);
    console.log(`Inisialisasi Baileys... mohon tunggu.`);
    connectToWhatsApp();
});

// ============================================================================
// SATPAM JARINGAN: Polling ke Server Laravel setiap 10 detik
// ============================================================================
setInterval(async () => {
    try {
        // Panggil endpoint health-check Laravel
        // Perbaikan: gunakan sub-domain 'iot.belajarhijaiyah.my.id' sesuai dengan APP_URL
        const isProduction = process.env.NODE_ENV === 'production' || __dirname.includes('public_html');
        const apiUrl = isProduction 
            ? 'https://iot.belajarhijaiyah.my.id/api/device/health-check' 
            : 'http://localhost:8000/api/device/health-check';

        const res = await fetch(apiUrl);
        if (!res.ok) return;

        const data = await res.json();
        if (data.status === 'went_offline') {
            console.log('\n[CRITICAL] ESP32 TERPUTUS! Mengirim notifikasi WA...');
        } else if (data.status === 'came_online') {
            console.log('\n[INFO] ESP32 KEMBALI ONLINE! Mengirim notifikasi WA...');
        }
    } catch (err) {
        // Diamkan jika server Laravel sedang mati agar log tidak penuh
    }
}, 10000);
