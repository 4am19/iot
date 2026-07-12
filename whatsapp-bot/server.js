const express = require('express');
const cors = require('cors');
const {
    default: makeWASocket,
    useMultiFileAuthState,
    DisconnectReason
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

// Format phone number to standard WhatsApp format (628xxxx@s.whatsapp.net)
const formatPhone = (phone) => {
    let formatted = phone.replace(/\D/g, ''); // Remove non-numeric
    if (formatted.startsWith('08')) {
        formatted = '628' + formatted.substring(2);
    } else if (formatted.startsWith('8')) {
        formatted = '628' + formatted.substring(1);
    }
    if (!formatted.endsWith('@s.whatsapp.net')) {
        formatted += '@s.whatsapp.net';
    }
    return formatted;
};

// Initialize WhatsApp connection
async function connectToWhatsApp() {
    const authPath = path.join(__dirname, 'auth_info_baileys');
    const { state, saveCreds } = await useMultiFileAuthState(authPath);

    sock = makeWASocket({
        auth: state,
        printQRInTerminal: false,
        logger: pino({ level: 'trace' }), // Enable verbose logging to see why Hostinger drops connection
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
            console.log('Koneksi terputus. Alasan:', lastDisconnect.error?.message);
            if (shouldReconnect) {
                console.log('Mencoba menyambung kembali...');
                connectToWhatsApp();
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

// API Endpoint to send broadcast message
app.post('/send-broadcast', async (req, res) => {
    if (!isConnected || !sock) {
        return res.status(503).json({ success: false, message: 'WhatsApp bot is not connected yet.' });
    }

    const { numbers, message } = req.body;

    if (!numbers || !Array.isArray(numbers) || numbers.length === 0) {
        return res.status(400).json({ success: false, message: 'Numbers array is required and cannot be empty.' });
    }
    if (!message) {
        return res.status(400).json({ success: false, message: 'Message text is required.' });
    }

    const results = [];

    for (const phone of numbers) {
        if (!phone || phone.trim() === '') continue;
        
        const formattedJid = formatPhone(phone);
        try {
            // Check if number exists on WhatsApp (optional but recommended)
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
        
        // Delay 1-2 seconds between messages to avoid ban
        await new Promise(resolve => setTimeout(resolve, 1500));
    }

    res.json({
        success: true,
        message: 'Broadcast completed',
        results
    });
});

app.listen(PORT, () => {
    console.log(`\n==============================================`);
    console.log(`🚀 Node.js WhatsApp Microservice`);
    console.log(`==============================================`);
    console.log(`Inisialisasi Baileys... mohon tunggu.`);
    connectToWhatsApp();
});
