<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Antrian Aftap – UTD</title>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --red:    #e53e3e;
            --red2:   #c53030;
            --blue:   #2b6cb0;
            --green:  #276749;
            --bg:     #0f172a;
            --bg2:    #1e293b;
            --card:   rgba(255,255,255,0.05);
            --border: rgba(255,255,255,0.08);
            --text:   #e2e8f0;
            --muted:  rgba(255,255,255,0.35);
        }

        body {
            background: var(--bg);
            font-family: 'Segoe UI', system-ui, sans-serif;
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* ══ HEADER ══ */
        .header {
            background: linear-gradient(90deg, #7f1d1d 0%, #991b1b 50%, #7f1d1d 100%);
            padding: 16px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 24px rgba(229,62,62,0.3);
            flex-shrink: 0;
        }
        .header-left { display: flex; align-items: center; gap: 14px; }
        .logo-circle {
            width: 48px; height: 48px;
            background: #fff; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; font-weight: 900; color: var(--red2); flex-shrink: 0;
        }
        .header-title { font-size: 1.5rem; font-weight: 800; letter-spacing: 1px; }
        .header-sub   { font-size: 0.75rem; opacity: .7; letter-spacing: 2px; text-transform: uppercase; margin-top: 2px; }
        .header-clock {
            font-size: 2.4rem; font-weight: 200;
            font-variant-numeric: tabular-nums; letter-spacing: 3px; opacity: .9;
        }

        /* ══ CONTENT ══ */
        .content {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 320px;
            grid-template-rows: auto 1fr;
            gap: 20px;
            padding: 20px 30px;
            overflow: hidden;
        }

        /* ── PANGGILAN UTAMA ── */
        .main-call {
            grid-column: 1; grid-row: 1;
            background: linear-gradient(135deg, rgba(229,62,62,0.18), rgba(127,29,29,0.12));
            border: 2px solid rgba(229,62,62,0.45);
            border-radius: 20px;
            padding: 30px 40px;
            display: flex; align-items: center; gap: 30px;
            min-height: 160px;
            position: relative; overflow: hidden;
        }
        .main-call::after {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(ellipse at 30% 50%, rgba(229,62,62,0.12) 0%, transparent 70%);
            pointer-events: none;
        }
        .main-call.empty { border-color: var(--border); background: var(--card); }

        .no-antrian-big {
            flex-shrink: 0;
            width: 170px; height: 120px;
            background: linear-gradient(135deg, var(--red), var(--red2));
            border-radius: 16px;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            box-shadow: 0 8px 38px rgba(229,62,62,0.4);
        }
        .no-antrian-big .label { font-size: 0.6rem; letter-spacing: 2px; text-transform: uppercase; opacity: .8; }
        /* ★ nomor antrian 4 digit — font lebih kecil agar muat */
        .no-antrian-big .num {
            font-size: 3rem;
            font-weight: 900;
            line-height: 1;
            letter-spacing: 3px;
        }
        .main-call-info { flex: 1; }
        .main-call-info .mc-label {
            font-size: 0.72rem; letter-spacing: 3px; text-transform: uppercase;
            color: #fc8181; margin-bottom: 6px;
        }
        .main-call-info .mc-nama { font-size: 2.4rem; font-weight: 800; line-height: 1.1; }
        .main-call-info .mc-meta {
            margin-top: 8px; font-size: 0.82rem; color: var(--muted);
            display: flex; gap: 20px; flex-wrap: wrap;
        }

        .bed-badge-big {
            flex-shrink: 0;
            background: linear-gradient(135deg, #1e40af, #1d4ed8);
            border-radius: 16px; padding: 16px 28px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(29,78,216,0.4);
        }
        .bed-badge-big .bl { font-size: 0.65rem; letter-spacing: 2px; text-transform: uppercase; opacity: .7; }
        .bed-badge-big .bn { font-size: 2.8rem; font-weight: 900; line-height: 1.1; }
        .empty-label { font-size: 1.1rem; color: var(--muted); font-weight: 300; letter-spacing: 1px; margin: auto; }

        /* ── RIWAYAT (kanan) ── */
        .history-panel {
            grid-column: 2; grid-row: 1 / 3;
            background: var(--card); border: 1px solid var(--border);
            border-radius: 20px;
            display: flex; flex-direction: column; overflow: hidden;
        }
        .history-panel .hp-header {
            padding: 16px 20px; border-bottom: 1px solid var(--border);
            font-size: 0.72rem; letter-spacing: 3px; text-transform: uppercase;
            color: var(--muted); flex-shrink: 0;
        }
        .history-list { flex: 1; overflow-y: auto; padding: 12px; display: flex; flex-direction: column; gap: 8px; }
        .history-list::-webkit-scrollbar { width: 4px; }
        .history-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 4px; }

        .h-item {
            background: rgba(255,255,255,0.04); border: 1px solid var(--border);
            border-radius: 10px; padding: 10px 14px;
            display: flex; align-items: center; gap: 10px; transition: all .3s;
        }
        .h-item.latest { background: rgba(229,62,62,0.12); border-color: rgba(229,62,62,0.35); }
        .h-item .h-no {
            min-width: 74px; height: 36px; flex-shrink: 0;
            background: rgba(255,255,255,0.08); border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.72rem; font-weight: 700; letter-spacing: 1px;
        }
        .h-item.latest .h-no { background: rgba(229,62,62,0.3); color: #fc8181; }
        .h-item .h-body { flex: 1; min-width: 0; }
        .h-item .h-nama { font-size: 0.82rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .h-item .h-sub  { font-size: 0.7rem; color: var(--muted); margin-top: 2px; }
        .h-item .h-bed  {
            flex-shrink: 0; background: rgba(29,78,216,0.25); color: #93c5fd;
            border-radius: 6px; padding: 3px 8px; font-size: 0.72rem; font-weight: 700;
        }

        /* ── BED GRID ── */
        .beds-panel {
            grid-column: 1; grid-row: 2;
            display: flex; flex-direction: column; gap: 10px; overflow: hidden;
        }
        .beds-label { font-size: 0.7rem; letter-spacing: 3px; text-transform: uppercase; color: var(--muted); }
        .beds-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 12px; flex: 1; }

        .bed-card {
            background: rgba(255,255,255,0.04); border: 1px solid var(--border);
            border-radius: 14px; padding: 12px 8px;
            text-align: center; transition: all .3s;
            display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2px;
        }
        .bed-card.occupied { background: rgba(21,128,61,0.15); border-color: rgba(21,128,61,0.4); }
        .bed-card.active {
            background: rgba(229,62,62,0.18); border-color: rgba(229,62,62,0.6);
            animation: pulse-card 1.8s ease-in-out infinite;
        }
        @keyframes pulse-card {
            0%,100% { box-shadow: 0 0 0 0 rgba(229,62,62,0.3); }
            50%      { box-shadow: 0 0 0 8px rgba(229,62,62,0); }
        }

        /* ★ DIPERBESAR: nomor bed & label teks donor */
        .bed-card .bc-num   { font-size: 2.4rem; font-weight: 500; line-height: 1; }
        .bed-card .bc-lbl   { font-size: 0.7rem; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); }
        .bed-card .bc-donor {
            font-size: 0.9rem;   /* ★ diperbesar dari 0.7rem */
            font-weight: 700;
            margin-top: 6px;
            color: #6ee7b7;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 90%;
        }
        .bed-card.active .bc-donor { color: #fca5a5; }
        .bed-card .bc-no    { font-size: 1rem; font-weight: 800;color: #f8fafc;
        margin-top: 4px;
        letter-spacing: 1px; } /* ★ diperbesar */

        /* ══ FOOTER ══ */
        .footer {
            padding: 10px 30px; background: rgba(0,0,0,0.4);
            border-top: 1px solid var(--border);
            display: flex; justify-content: space-between; align-items: center;
            font-size: 0.72rem; color: var(--muted); flex-shrink: 0;
        }

        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .anim-in { animation: slideInLeft 0.5s ease forwards; }

        /* ══ OVERLAY AKTIFKAN SUARA ══ */
        /* Overlay fullscreen saat pertama buka */
        #sound_overlay {
            position: fixed; inset: 0; z-index: 9999;
            background: rgba(15,23,42,0.97);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center; gap: 24px;
            cursor: pointer;
        }
        #sound_overlay .ov-icon { font-size: 5rem; }
        #sound_overlay .ov-title { font-size: 1.8rem; font-weight: 800; color: #e2e8f0; }
        #sound_overlay .ov-sub   { font-size: 1rem; color: rgba(255,255,255,0.5); }
        #sound_overlay .ov-btn   {
            margin-top: 12px;
            background: var(--red); color: #fff;
            border: none; border-radius: 50px;
            padding: 14px 40px; font-size: 1.1rem; font-weight: 700;
            cursor: pointer; transition: all .2s;
        }
        #sound_overlay .ov-btn:hover { background: var(--red2); transform: scale(1.04); }

        /* Tombol kecil pojok (setelah overlay ditutup) */
        .sound-btn {
            position: fixed; bottom: 24px; right: 24px;
            background: rgba(255,255,255,0.1); border: 1px solid var(--border);
            color: var(--text); border-radius: 50px; padding: 10px 20px;
            font-size: 0.8rem; cursor: pointer;
            display: none; /* tampil setelah overlay ditutup */
            align-items: center; gap: 8px; transition: all .2s; z-index: 99;
        }
        .sound-btn:hover { background: rgba(255,255,255,0.18); }
        .sound-btn.on    { background: rgba(21,128,61,0.3); border-color: rgba(21,128,61,0.5); }
        .sound-dot { width: 8px; height: 8px; border-radius: 50%; background: #f87171; }
        .sound-btn.on .sound-dot { background: #4ade80; }
    </style>
</head>
<body>

    {{-- ══ OVERLAY: klik sekali untuk aktifkan audio ══ --}}
    <div id="sound_overlay" onclick="activateSound()">
        <div class="ov-icon">🔊</div>
        <div class="ov-title">Display Antrian Aftap</div>
        <div class="ov-sub">Klik di mana saja untuk mengaktifkan tampilan & suara</div>
        <button class="ov-btn">Mulai Tampilan</button>
    </div>

    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <div class="logo-circle">🩸</div>
            <div>
                <div class="header-title">ANTRIAN AFTAP</div>
                <div class="header-sub">Unit Transfusi Darah — PMI</div>
            </div>
        </div>
        <div class="header-clock" id="clock">00:00:00</div>
    </div>

    <!-- Content -->
    <div class="content">
        <!-- Panggilan terbaru -->
        <div class="main-call empty" id="main_call">
            <div class="empty-label">⏳ Menunggu Panggilan...</div>
        </div>

        <!-- Riwayat -->
        <div class="history-panel">
            <div class="hp-header">📋 Riwayat Panggilan</div>
            <div class="history-list" id="history_list">
                <div style="color:var(--muted); font-size:0.82rem; padding:12px;">Belum ada panggilan hari ini.</div>
            </div>
        </div>

        <!-- Status 10 Bed -->
        <div class="beds-panel">
            <div class="beds-label">🛏 Status Bed</div>
            <div class="beds-grid">
                @for($i = 1; $i <= 18; $i++)
                    <div class="bed-card" id="bed_{{ $i }}">
                        <div class="bc-num">{{ $i }}</div>
                        <div class="bc-lbl">BED</div>
                        <div class="bc-donor" id="bed_donor_{{ $i }}">Kosong</div>
                        <div class="bc-no"   id="bed_no_{{ $i }}"></div>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <span>Sistem Antrian Digital – UTD PMI</span>
        <span id="footer_date"></span>
    </div>

    <!-- Tombol toggle suara (muncul setelah overlay) -->
    <button class="sound-btn on" id="sound_btn" onclick="toggleSound()">
        <span class="sound-dot"></span>
        <span id="sound_label">Suara Aktif</span>
    </button>

 <script>
    /* ════════ JAM ════════ */
    const pad = n => String(n).padStart(2,'0');
    const tick = () => {
        const d = new Date();
        document.getElementById('clock').textContent =
            `${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
        document.getElementById('footer_date').textContent =
            d.toLocaleDateString('id-ID',{weekday:'long',year:'numeric',month:'long',day:'numeric'});
    };
    setInterval(tick, 1000); tick();

    /* ════════ SUARA ════════ */
    let soundEnabled = false;
    let voices = [];
    const loadVoices = () => { voices = speechSynthesis.getVoices(); };
    speechSynthesis.onvoiceschanged = loadVoices;
    loadVoices();

    const speak = (text) => {
        if (!soundEnabled || !window.speechSynthesis) return;
        speechSynthesis.cancel();
        const utt = new SpeechSynthesisUtterance(text);
        utt.lang   = 'id-ID';
        utt.rate   = 0.88;
        utt.pitch  = 1.0;
        utt.volume = 1.0;
        const idVoice = voices.find(v =>
            v.lang.startsWith('id') || v.name.toLowerCase().includes('indonesia')
        );
        if (idVoice) utt.voice = idVoice;
        speechSynthesis.speak(utt);
    };

    
    const buildAnnouncement = (item) => {

        const nomor = item.nomor_antrian ?? 'B000';

        // pisah prefix dan angka
        const prefix = nomor.substring(0, 1);
        const angka  = nomor.substring(1);

        const nama = item.nama;
        const bed  = item.bed;

        return `Nomor antrian ${prefix} ${angka}. Atas nama ${nama}. Silakan menuju bed ${bed}.`;
    };

    /* ════════ RENDER ════════ */
    let lastCalledId = null;

    const renderLatest = (item) => {
        if (item.aftap_id === lastCalledId) return;
        lastCalledId = item.aftap_id;

       const lengan = (item.lengan || '').toLowerCase().trim();

        const isKanan = lengan === 'kanan';

        const lenganColor = isKanan ? '#60a5fa' : '#fbbf24';

        const lenganLabel = isKanan
            ? '🅐 Lengan Kanan'
            : '🅑 Lengan Kiri';
        const mc = document.getElementById('main_call');
        mc.classList.remove('empty');
        mc.classList.add('anim-in');
        mc.innerHTML = `
            <div class="no-antrian-big">
                <div class="label">No.Antrian</div>
                <div class="num">${item.nomor_antrian ?? '–'}</div>
            </div>
            <div class="main-call-info">
                <div class="mc-label">📢 Dipanggil Sekarang</div>
                <div class="mc-nama">${item.nama}</div>
                <div class="mc-meta">
                <span style="color:${lenganColor};font-weight:700;">${lenganLabel}</span>
            </div>
            </div>
            <div class="bed-badge-big">
                <div class="bl">BED</div>
                <div class="bn">${item.bed}</div>
            </div>`;
        setTimeout(() => mc.classList.remove('anim-in'), 600);

        updateBedCard(item, true);
    };

    const updateBedCard = (item, isActive) => {
        const card = document.getElementById(`bed_${item.bed}`);
        if (!card) return;
        card.className = 'bed-card ' + (isActive ? 'active' : 'occupied');

        document.getElementById(`bed_donor_${item.bed}`).textContent =
            item.nama.split(' ').slice(0, 2).join(' ');
        document.getElementById(`bed_no_${item.bed}`).textContent =
            item.nomor_antrian ? item.nomor_antrian : '';
    };

        const render = (data) => {
        if (!data?.length) return;
        const latest = data[0];

        // ✅ speak hanya jika ada panggilan baru
        if (latest.aftap_id !== lastCalledId) {
            renderLatest(latest);
            setTimeout(() => speak(buildAnnouncement(latest)), 300);
        }

        // Reset semua bed
        for (let i = 1; i <= 18; i++) {
            document.getElementById(`bed_${i}`).className = 'bed-card';
            document.getElementById(`bed_donor_${i}`).textContent = 'Kosong';
            document.getElementById(`bed_no_${i}`).textContent = '';
        }

        // Isi dari data
        data.forEach((item, idx) => updateBedCard(item, idx === 0));

        // Riwayat
        document.getElementById('history_list').innerHTML =
            data.map((item, idx) => `
                <div class="h-item ${idx === 0 ? 'latest' : ''}">
                    <div class="h-no">${item.nomor_antrian ?? '–'}</div>
                    <div class="h-body">
                        <div class="h-nama">${item.nama}</div>
                        <div class="h-sub">${item.called_at}${item.petugas ? ` • ${item.petugas}` : ''}</div>
                    </div>
                    <div class="h-bed">Bed ${item.bed}</div>
                </div>`).join('');
    };

    /* ════════ POLLING ════════ */
    const poll = () => {
    fetch('{{ route("unit.aftap.display_antrian_data") }}')
        .then(r => {
            if (!r.ok) throw new Error(`HTTP ${r.status}`);
            return r.json();
        })
        .then(data => {
            // ✅ jika response adalah error object, skip render
            if (data?.error) {
                console.warn('Server error:', data.message);
                return;
            }
            render(data);
        })
        .catch(err => console.warn('Poll error:', err.message));
};

    /* ════════ AKTIVASI SUARA ════════ */
    const activateSound = () => {
        const unlock = new SpeechSynthesisUtterance(' ');
        unlock.volume = 0;
        speechSynthesis.speak(unlock);

        soundEnabled = true;
        document.getElementById('sound_overlay').style.display = 'none';
        const btn = document.getElementById('sound_btn');
        btn.style.display = 'flex';
        btn.classList.add('on');
        document.getElementById('sound_label').textContent = 'Suara Aktif';

        // ✅ mulai polling DB langsung, tanpa BroadcastChannel
        poll();
        setInterval(poll, 3000);

        setTimeout(() => speak('Sistem suara antrian aftap aktif'), 600);
    };

    const toggleSound = () => {
        soundEnabled = !soundEnabled;
        const btn = document.getElementById('sound_btn');
        btn.classList.toggle('on', soundEnabled);
        document.getElementById('sound_label').textContent =
            soundEnabled ? 'Suara Aktif' : 'Suara Mati';
        if (soundEnabled) speak('Suara diaktifkan');
    };
</script>
</body>
</html>