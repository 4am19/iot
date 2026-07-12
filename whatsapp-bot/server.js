const express = require('express');
const cors = require('cors');
const {
    default: makeWASocket,
    useMultiFileAuthState,
    DisconnectReason,
    makeCacheableSignalKeyStore,
    fetchLatestBaileysVersion
} = require('@whiskeysockets/baileys');
const pino = require('pino');
const path = require('path');
const readline = require('readline');

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

// Prompt user input from terminal
const question = (text) => {
    const rl = readline.createInterface({ input: process.stdin, output: process.stdout });
    return new Promise((resolve) => rl.question(text, (ans) => { rl.close(); resolve(ans); }));
};

// Initialize WhatsApp connection
async function connectToWhatsApp() {
    const authPath = path.join(__dirname, 'auth_info_baileys');
    const { state, saveCreds } = await useMultiFileAuthState(authPath);
    const { version } = await fetchLatestBaileysVersion();

    const logger = pino({ level: 'silent' });

    sock = makeWASocket({
        version,
        auth: {
            creds: state.creds,
            keys: makeCacheableSignalKeyStore(state.keys, logger),
        },
        printQRInTerminal: false,
        logger,
        browser: ['Chrome (Linux)', '', ''],
        generateHighQualityLinkPreview: false,
    });

    // Jika belum pernah login, gunakan Pairing Code
    if (!state.creds.registered) {
        console.log('\n========================================');
        console.log('   METODE LOGIN: PAIRING CODE');
        console.log('========================================\n');

        const phoneNumber = await question('Masukkan nomor WA Anda (contoh: 628123456789): ');
        const cleanNumber = phoneNumber.replace(/\D/g, '');

        // Tunggu sebentar agar koneksi WebSocket stabil
        await new Promise(resolve => setTimeout(resolve, 3000));

        try {
            const code = await sock.requestPairingCode(cleanNumber);
            console.log(`\n🔑 KODE PAIRING ANDA: ${code}\n`);
            console.log('Cara memasukkan kode:');
            console.log('1. Buka WhatsApp di HP Anda');
            console.log('2. Buka menu Perangkat Taut (Linked Devices)');
            console.log('3. Pilih "Tautkan dengan Nomor Telepon"');
            console.log('4. Masukkan kode di atas\n');
        } catch (err) {
            console.error('Gagal mendapatkan pairing code:', err.message);
            console.log('\nMencoba ulang dalam 10 detik...');
            await new Promise(resolve => setTimeout(resolve, 10000));
            connectToWhatsApp();
            return;
        }
    }

    sock.ev.on('connection.update', (update) => {
        const { connection, lastDisconnect } = update;

        if (connection === 'close') {
            isConnected = false;
            const statusCode = lastDisconnect?.error?.output?.statusCode;
            const shouldReconnect = statusCode !== DisconnectReason.loggedOut;
            console.log('Koneksi terputus. Status code:', statusCode);

            if (shouldReconnect) {
                console.log('Mencoba menyambung kembali dalam 5 detik...');
                setTimeout(() => connectToWhatsApp(), 5000); // Delay 5 detik
            } else {
                console.log('Sesi logout. Hapus folder "auth_info_baileys" dan jalankan ulang.');
            }
        } else if (connection === 'open') {
            isConnected = true;
            console.log('\n✅ WHATSAPP BOT BERHASIL TERHUBUNG!\n');
            console.log(`API server berjalan pada http://localhost:${PORT}`);
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

        // Delay 1.5 detik antar pesan untuk menghindari ban
        await new Promise(resolve => setTimeout(resolve, 1500));
    }

    res.json({
        success: true,
        message: 'Broadcast completed',
        results
    });
});

// Health check endpoint
app.get('/status', (req, res) => {
    res.json({ connected: isConnected });
});

app.listen(PORT, () => {
    console.log(`\n==============================================`);
    console.log(`🚀 Node.js WhatsApp Microservice`);
    console.log(`==============================================`);
    console.log(`Inisialisasi Baileys... mohon tunggu.\n`);
    connectToWhatsApp();
});
