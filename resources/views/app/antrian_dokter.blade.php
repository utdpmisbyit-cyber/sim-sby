<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrian Pemeriksaan Dokter</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;900&family=Barlow:wght@400;500;600&display=swap');

        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            min-height: 100vh;
            background: #f5f5f5;
            font-family: 'Barlow', sans-serif;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* ── DEKORASI LATAR ── */
        .deco {
            position: fixed;
            border-radius: 50%;
            opacity: 0.06;
            pointer-events: none;
            z-index: 0;
        }
        .deco-1 { width: 600px; height: 600px; background: #c0392b; top: -200px; left: -150px; }
        .deco-2 { width: 500px; height: 500px; background: #c0392b; bottom: -180px; right: -120px; }

        /* ── HEADER ── */
        header {
            background: #c0392b;
            padding: 16px 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
            position: relative;
            z-index: 1;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }

        .pmi-logo {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .pmi-cross {
            width: 48px;
            height: 48px;
            position: relative;
            flex-shrink: 0;
        }
        .pmi-cross::before,
        .pmi-cross::after {
            content: '';
            position: absolute;
            background: #fff;
            border-radius: 3px;
        }
        .pmi-cross::before { width: 16px; height: 48px; left: 50%; transform: translateX(-50%); }
        .pmi-cross::after  { width: 48px; height: 16px; top: 50%;  transform: translateY(-50%); }

        .header-center {
            text-align: center;
        }
        .header-title {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: 2px;
            text-transform: uppercase;
            display: block;
        }
        .header-sub {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.8);
            margin-top: 2px;
            letter-spacing: 1px;
        }

        .header-clock {
            text-align: right;
        }
        .clock-time {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 2.2rem;
            font-weight: 700;
            color: #fff;
            font-variant-numeric: tabular-nums;
            letter-spacing: 2px;
            display: block;
        }
        .clock-date {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.85);
            margin-top: 2px;
        }

        /* ── MAIN ── */
        main {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 32px 48px;
            position: relative;
            z-index: 1;
            gap: 24px;
        }

        .section-label {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: #c0392b;
        }

        /* ── DUA CARD RUANGAN ── */
        .rooms-wrapper {
            display: flex;
            gap: 32px;
            width: 100%;
            max-width: 1100px;
        }

        .queue-card {
            flex: 1;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.12);
            padding: 36px 32px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
            border-top: 6px solid #c0392b;
            transition: transform 0.3s;
        }
        .queue-card.room-2 { border-top-color: #8e44ad; }

        /* Animasi saat dipanggil */
        .queue-card.calling {
            animation: cardPulse 1.5s ease-in-out infinite;
        }
        @keyframes cardPulse {
            0%,100% { box-shadow: 0 8px 40px rgba(0,0,0,0.12); }
            50%      { box-shadow: 0 8px 60px rgba(192,57,43,0.4); }
        }
        .queue-card.room-2.calling {
            animation: cardPulse2 1.5s ease-in-out infinite;
        }
        @keyframes cardPulse2 {
            0%,100% { box-shadow: 0 8px 40px rgba(0,0,0,0.12); }
            50%      { box-shadow: 0 8px 60px rgba(142,68,173,0.4); }
        }

        .room-tag {
            position: absolute;
            top: 14px;
            left: 18px;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 20px;
            color: #fff;
        }
        .room-tag.r1 { background: #c0392b; }
        .room-tag.r2 { background: #8e44ad; }

        .calling-badge {
            position: absolute;
            top: 14px;
            right: 18px;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
            letter-spacing: 1px;
        }
        .calling-badge.waiting { background: #f0f0f0; color: #999; }
        .calling-badge.active-r1 {
            background: #fff3e0; color: #c0392b;
            animation: pulse 1.5s infinite;
        }
        .calling-badge.active-r2 {
            background: #f3e5f5; color: #8e44ad;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }

        .no-label {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #aaa;
            margin-top: 24px;
        }

        .queue-number {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: clamp(4rem, 10vw, 7rem);
            font-weight: 900;
            line-height: 1;
            margin-top: 4px;
        }
        .queue-card:not(.room-2) .queue-number { color: #c0392b; }
        .queue-card.room-2        .queue-number { color: #8e44ad; }
        .queue-card.room-hb .divider { background:#16a085; }
        .queue-card.room-1 { border-top-color: #2980b9; }
        .queue-card.room-2 { border-top-color: #8e44ad; }
        .queue-card.room-3 { border-top-color: #c0392b; }
        .queue-card.room-hb { border-top-color: #16a085; }

        .room-tag.r1 { background: #2980b9; }
        .room-tag.r2 { background: #8e44ad; }
        .room-tag.r3 { background: #c0392b; }
        .room-tag.hb { background: #16a085; }

        .queue-card.room-1 .queue-number { color: #2980b9; }
        .queue-card.room-2 .queue-number { color: #8e44ad; }
        .queue-card.room-3 .queue-number { color: #c0392b; }
        .queue-card.room-hb .queue-number { color: #16a085; }

        .queue-card.room-1 .divider { background: #2980b9; }
        .queue-card.room-2 .divider { background: #8e44ad; }
        .queue-card.room-3 .divider { background: #c0392b; }
        .queue-card.room-hb .divider { background: #16a085; }

        .queue-card.room-1 .room-badge {
            background: #eaf4fb;
            color: #2980b9;
        }

        .queue-card.room-2 .room-badge {
            background: #f3e5f5;
            color: #8e44ad;
        }

        .queue-card.room-3 .room-badge {
            background: #fdecea;
            color: #c0392b;
        }

        .queue-card.room-hb .room-badge {
            background: #e8f8f5;
            color: #16a085;
        }
        .divider {
            width: 60px;
            height: 3px;
            border-radius: 2px;
            margin: 14px 0;
        }
        .queue-card:not(.room-2) .divider { background: #c0392b; }
        .queue-card.room-2        .divider { background: #8e44ad; }
        .queue-card.room-hb .room-badge { background:#e8f8f5; color:#16a085; }

        .patient-name {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: clamp(1.4rem, 3.5vw, 2.2rem);
            font-weight: 700;
            color: #222;
            word-break: break-word;
        }

        .dokter-name {
            font-size: 0.9rem;
            color: #666;
            margin-top: 6px;
        }

        .room-badge {
            margin-top: 14px;
            padding: 8px 20px;
            border-radius: 30px;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .queue-card:not(.room-2) .room-badge { background: #fdecea; color: #c0392b; }
        .queue-card.room-2        .room-badge { background: #f3e5f5; color: #8e44ad; }

        /* ── DAFTAR ANTRIAN (bawah) ── */
        .queue-list-section {
            width: 100%;
            max-width: 1100px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            max-height: 220px;
        }
        .queue-list-header {
            background: #c0392b;
            padding: 10px 24px;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #fff;
        }
        .queue-list-body {
            overflow-y: auto;
            max-height: 170px;
        }
        .q-row {
            display: flex;
            align-items: center;
            padding: 10px 24px;
            border-bottom: 1px solid #f5f5f5;
            gap: 12px;
        }
        .q-row:last-child { border-bottom: none; }
        .q-num {
            width: 32px; height: 32px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.85rem;
            flex-shrink: 0;
            color: #fff;
        }
        .q-num.waiting  { background: #bdc3c7; }
        .q-num.called-r1{ background: #c0392b; }
        .q-num.called-r2{ background: #8e44ad; }
        .q-num.process  { background: #2980b9; }
        .q-num.done     { background: #27ae60; }

        .q-row-info { flex: 1; }
        .q-row-nama { font-size: 0.9rem; font-weight: 600; color: #222; }
        .q-row-meta { font-size: 0.72rem; color: #aaa; margin-top: 1px; }

        .q-pill {
            font-size: 0.65rem; font-weight: 700;
            padding: 3px 10px; border-radius: 20px;
        }
        .q-pill.waiting   { background: #ecf0f1; color: #7f8c8d; }
        .q-pill.called-r1 { background: #fdecea; color: #c0392b; }
        .q-pill.called-r2 { background: #f3e5f5; color: #8e44ad; }
        .q-pill.process   { background: #eaf4fb; color: #2980b9; }
        .q-pill.done      { background: #eafaf1; color: #27ae60; }

        .q-room-pill {
            font-size: 0.62rem; font-weight: 700;
            padding: 3px 8px; border-radius: 12px;
            color: #fff; margin-left: 4px;
        }
        .q-room-pill.r1 { background: #c0392b; }
        .q-room-pill.r2 { background: #8e44ad; }

        /* ── FOOTER TICKER ── */
        footer {
            background: #c0392b;
            padding: 7px 0;
            overflow: hidden;
            white-space: nowrap;
            flex-shrink: 0;
            position: relative;
            z-index: 1;
        }
        .ticker-inner {
            display: inline-block;
            animation: scroll 35s linear infinite;
            font-size: 0.82rem;
            color: rgba(255,255,255,0.9);
            letter-spacing: 1px;
            padding-right: 80px;
        }
        .rooms-wrapper {
            display: flex;
            gap: 24px;
            width: 100%;
            max-width: 1700px;
        }

        .queue-card {
            flex: 1;
        }

        @keyframes scroll { from{transform:translateX(100vw)} to{transform:translateX(-100%)} }

        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: #ddd; border-radius: 2px; }
    </style>
</head>
<body>

<div class="deco deco-1"></div>
<div class="deco deco-2"></div>

{{-- HEADER --}}
<header>
    <div class="pmi-logo">
        <div class="pmi-cross"></div>
        <span style="font-family:'Barlow Condensed',sans-serif;font-size:1.1rem;font-weight:700;color:#fff;letter-spacing:1px;">
            Palang Merah Indonesia
        </span>
    </div>

    <div class="header-center">
        <span class="header-title">Antrian Pemeriksaan </span>
        <span class="header-sub">Unit Transfusi Darah</span>
    </div>

    <div class="header-clock">
        <span class="clock-time" id="clock">00:00:00</span>
        <div class="clock-date" id="tanggal"></div>
    </div>
</header>

{{-- MAIN --}}
<main>
    <div class="section-label">&#x1F50A; Sedang Dipanggil</div>

   {{-- TIGA CARD RUANGAN + HB --}}
    <div class="rooms-wrapper">

        {{-- Ruangan 1 --}}
        <div class="queue-card room-1" id="card-r1">
            <span class="room-tag r1">Ruangan 1</span>
            <span class="calling-badge waiting" id="badge-r1">Menunggu</span>

            <div class="no-label">No. Antrian</div>
            <div class="queue-number" id="num-r1">-</div>

            <div class="divider"></div>

            <div class="patient-name" id="nama-r1">-</div>
            <div class="dokter-name" id="dokter-r1"></div>

            <div class="room-badge">Ruangan Dokter 1</div>
        </div>

        {{-- Ruangan 2 --}}
        <div class="queue-card room-2" id="card-r2">
            <span class="room-tag r2">Ruangan 2</span>
            <span class="calling-badge waiting" id="badge-r2">Menunggu</span>

            <div class="no-label">No. Antrian</div>
            <div class="queue-number" id="num-r2">-</div>

            <div class="divider"></div>

            <div class="patient-name" id="nama-r2">-</div>
            <div class="dokter-name" id="dokter-r2"></div>

            <div class="room-badge">Ruangan Dokter 2</div>
        </div>

        {{-- Ruangan 3 --}}
        <div class="queue-card room-3" id="card-r3">
            <span class="room-tag r3">Ruangan 3</span>
            <span class="calling-badge waiting" id="badge-r3">Menunggu</span>

            <div class="no-label">No. Antrian</div>
            <div class="queue-number" id="num-r3">-</div>

            <div class="divider"></div>

            <div class="patient-name" id="nama-r3">-</div>
            <div class="dokter-name" id="dokter-r3"></div>

            <div class="room-badge">Ruangan Dokter 3</div>
        </div>

        {{-- HB --}}
        <div class="queue-card room-hb" id="card-hb">
            <span class="room-tag hb">HB</span>
            <span class="calling-badge waiting" id="badge-hb">Menunggu</span>

            <div class="no-label">No. Antrian</div>
            <div class="queue-number" id="num-hb">-</div>

            <div class="divider"></div>

            <div class="patient-name" id="nama-hb">-</div>
            <div class="dokter-name" id="dokter-hb"></div>

            <div class="room-badge">Pemeriksaan HB</div>
        </div>

    </div>

    {{-- DAFTAR ANTRIAN --}}
    <div class="queue-list-section">
        <div class="queue-list-header">
            Daftar Antrian Hari Ini &nbsp;<span id="q-count" style="opacity:.8"></span>
        </div>
        <div class="queue-list-body" id="q-list">
            <div style="text-align:center;padding:30px;color:#aaa;font-size:0.85rem">
                Memuat data antrian...
            </div>
        </div>
    </div>
</main>

{{-- FOOTER TICKER --}}
<footer>
    <span class="ticker-inner">
        Selamat datang di Unit Transfusi Darah &nbsp;|&nbsp;
        Harap memperhatikan layar untuk panggilan antrian &nbsp;|&nbsp;
        Terima kasih atas kepedulian Anda mendonorkan darah &nbsp;|&nbsp;
        Donor darah: satu tindakan, tiga nyawa terselamatkan
    </span>
</footer>

<script>
const DATA_URL = '{{ url("/unit/pemeriksaan_kesehatan/display-antrian/data") }}';

/* ===============================
   CLOCK
================================ */
document.addEventListener('DOMContentLoaded', () => {

    const pad = n => String(n).padStart(2, '0');

    function updateClock() {

        const now = new Date();

        document.getElementById('clock').innerHTML =
            pad(now.getHours()) + ':' +
            pad(now.getMinutes()) + ':' +
            pad(now.getSeconds());

        const hari = [
            'Minggu','Senin','Selasa',
            'Rabu','Kamis','Jumat','Sabtu'
        ];

        const bulan = [
            'Januari','Februari','Maret',
            'April','Mei','Juni',
            'Juli','Agustus','September',
            'Oktober','November','Desember'
        ];

        document.getElementById('tanggal').innerHTML =
            hari[now.getDay()] + ', ' +
            now.getDate() + ' ' +
            bulan[now.getMonth()] + ' ' +
            now.getFullYear();
    }

    updateClock();
    setInterval(updateClock, 1000);

});

/* ===============================
   UPDATE CARD
================================ */
function updateCard(type, data) {

    const map = {
        r1: {
            num: 'num-r1',
            nama: 'nama-r1',
            dokter: 'dokter-r1',
            card: 'card-r1',
            badge: 'badge-r1',
            color: 'active-r1'
        },

        r2: {
            num: 'num-r2',
            nama: 'nama-r2',
            dokter: 'dokter-r2',
            card: 'card-r2',
            badge: 'badge-r2',
            color: 'active-r2'
        },

        r3: {
            num: 'num-r3',
            nama: 'nama-r3',
            dokter: 'dokter-r3',
            card: 'card-r3',
            badge: 'badge-r3',
            color: 'active-r1'
        },

        hb: {
            num: 'num-hb',
            nama: 'nama-hb',
            dokter: 'dokter-hb',
            card: 'card-hb',
            badge: 'badge-hb',
            color: 'active-r1'
        }
    };

    const m = map[type];

    if (!m) return;

    if (data) {

        document.getElementById(m.num).innerHTML =
            data.no || '-';

        document.getElementById(m.nama).innerHTML =
            data.nama || '-';

        document.getElementById(m.dokter).innerHTML =
            data.dokter || '';

        document.getElementById(m.card)
            .classList.add('calling');

        document.getElementById(m.badge)
            .className = `calling-badge ${m.color}`;

        document.getElementById(m.badge)
            .innerHTML = 'Dipanggil';

    } else {

        document.getElementById(m.num).innerHTML = '-';
        document.getElementById(m.nama).innerHTML = '-';
        document.getElementById(m.dokter).innerHTML = '';

        document.getElementById(m.card)
            .classList.remove('calling');

        document.getElementById(m.badge)
            .className = 'calling-badge waiting';

        document.getElementById(m.badge)
            .innerHTML = 'Menunggu';
    }
}

/* ===============================
   FETCH DATA
================================ */
function fetchData() {

    fetch(DATA_URL)

        .then(res => res.json())

        .then(res => {

            const donors =
                Array.isArray(res)
                    ? res
                    : (res.data || []);

            renderAll(donors);
        })

        .catch(err => {
            console.error('ERROR FETCH:', err);
        });
}

/* ===============================
   RENDER
================================ */
function renderAll(donors) {

    console.log('DONORS:', donors);

    document.getElementById('q-count').innerHTML =
        '(' + donors.length + ')';

    let activeR1 = null;
    let activeR2 = null;
    let activeR3 = null;
    let activeHB = null;

    let html = '';

    donors.forEach((d) => {

        console.log('ITEM:', d);

        const no =
            (d.kode || '').slice(-3);

        const nama =
            d.donor?.nama || '-';

        const step =
            (d.step || '').trim();

        const ruangan =
            parseInt(
                d.nomor_ruangan ||
                d.pemeriksaan_dokter?.nomor_ruangan ||
                1
            );

        let dokterNama = '';

        if (d.pemeriksaan_dokter?.dokter?.nama) {
            dokterNama =
                'Dr. ' +
                d.pemeriksaan_dokter.dokter.nama;
        }

        if (d.pemeriksaan_hb?.dokter?.nama) {
            dokterNama =
                'HB. ' +
                d.pemeriksaan_hb.dokter.nama;
        }

        /* =========================
           SET DISPLAY CARD
        ========================= */

        if (
            step.toLowerCase() === 'kesehatan'
        ) {

            const payload = {
                no,
                nama,
                dokter: dokterNama
            };

            if (ruangan === 1) activeR1 = payload;
            if (ruangan === 2) activeR2 = payload;
            if (ruangan === 3) activeR3 = payload;
        }

        if (
            step.toLowerCase() === 'hb'
        ) {

            activeHB = {
                no,
                nama,
                dokter: dokterNama
            };
        }

        html += `
            <div class="q-row">
                <div class="q-num called-r1">
                    ${no}
                </div>

                <div class="q-row-info">
                    <div class="q-row-nama">
                        ${nama}
                    </div>

                    <div class="q-row-meta">
                        ${dokterNama}
                    </div>
                </div>

                <span class="q-pill called-r1">
                    ${step}
                </span>
            </div>
        `;
    });

    document.getElementById('q-list').innerHTML = html;

    console.log('ACTIVE R1:', activeR1);
    console.log('ACTIVE R2:', activeR2);
    console.log('ACTIVE R3:', activeR3);
    console.log('ACTIVE HB:', activeHB);

    updateCard('r1', activeR1);
    updateCard('r2', activeR2);
    updateCard('r3', activeR3);
    updateCard('hb', activeHB);
}

/* ===============================
   INIT
================================ */
fetchData();

setInterval(fetchData, 2000);
</script>
</body>
</html>


























