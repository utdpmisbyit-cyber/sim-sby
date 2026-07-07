<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Display Antrian Apheresis</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #0b1d3a;
            color: #fff;
            min-height: 100vh;
        }
        header {
            text-align: center;
            padding: 20px 10px 10px;
        }
        header h1 {
            margin: 0;
            font-size: 2.2rem;
            letter-spacing: 1px;
        }
        header .clock {
            font-size: 1.2rem;
            color: #9db4d6;
            margin-top: 4px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 18px;
            padding: 20px 30px 40px;
        }
        .card {
            background: #12295a;
            border-radius: 14px;
            padding: 18px 20px;
            box-shadow: 0 4px 14px rgba(0,0,0,.25);
            border-left: 6px solid #f5a623;
        }
        .card .kode {
            font-size: 2.4rem;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .card .nama {
            font-size: 1.1rem;
            color: #cfe0ff;
            margin: 6px 0 10px;
            min-height: 1.4em;
        }
        .card .info {
            display: flex;
            justify-content: space-between;
            font-size: .95rem;
            color: #9db4d6;
            border-top: 1px solid rgba(255,255,255,.1);
            padding-top: 8px;
        }
        .card .ruangan {
            font-weight: 600;
            color: #f5a623;
        }
        .empty-state {
            text-align: center;
            color: #9db4d6;
            font-size: 1.3rem;
            padding: 60px 20px;
        }
    </style>
</head>
<body>

    <header>
        <h1>Antrian Apheresis</h1>
        <div class="clock" id="clock"></div>
    </header>

    <div class="grid" id="grid-antrian">
        <div class="empty-state">Memuat data antrian...</div>
    </div>

    <script>
    (function () {
        const dataUrl = @json(route('antrian_apheresis.data'));
        const grid = document.getElementById('grid-antrian');
        const clockEl = document.getElementById('clock');
        const REFRESH_MS = 5000;

        function tickClock() {
            const now = new Date();
            clockEl.textContent = now.toLocaleString('id-ID', {
                weekday: 'long', day: '2-digit', month: 'long', year: 'numeric',
                hour: '2-digit', minute: '2-digit', second: '2-digit',
            });
        }

        function renderCard(item) {
            const namaDokter = item.pemeriksaan_dokter?.dokter?.nama
                ?? item.pemeriksaan_hb?.dokter?.nama
                ?? '-';
            const ruangan = item.pemeriksaan_dokter?.nomor_ruangan ?? item.nomor_ruangan ?? '-';

            const div = document.createElement('div');
            div.className = 'card';
            div.innerHTML = `
                <div class="kode">${item.kode ?? '-'}</div>
                <div class="nama">${item.donor?.nama ?? '-'}</div>
                <div class="info">
                    <span>Dokter: ${namaDokter}</span>
                    <span class="ruangan">Ruang ${ruangan}</span>
                </div>
            `;
            return div;
        }

        async function loadData() {
            try {
                const response = await fetch(dataUrl, { headers: { 'Accept': 'application/json' } });
                if (!response.ok) throw new Error('Gagal mengambil data');

                const items = await response.json();

                grid.innerHTML = '';

                if (!items.length) {
                    grid.innerHTML = '<div class="empty-state">Belum ada antrian apheresis hari ini.</div>';
                    return;
                }

                items.forEach((item) => grid.appendChild(renderCard(item)));
            } catch (e) {
                grid.innerHTML = '<div class="empty-state">Gagal memuat data antrian. Mencoba lagi...</div>';
            }
        }

        tickClock();
        setInterval(tickClock, 1000);

        loadData();
        setInterval(loadData, REFRESH_MS);
    })();
    </script>

</body>
</html>