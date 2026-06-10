@extends('layouts.index')

@section('title', 'Form Permintaan Darah - Penyimpanan')

@push('styles')
<style>
/* Semua style yang sama seperti sebelumnya */
* { box-sizing: border-box; margin: 0; padding: 0; }
.page { padding: 1.5rem; }
/* ... style lainnya ... */
</style>
@endpush

@section('content')
<div class="page">
    <div class="header-bar">
        <h1><i class="ti ti-droplet" style="font-size:20px;color:#D85A30"></i> Permintaan Darah Baru</h1>
        <span class="badge-status"><i class="ti ti-circle-check"></i> Mode Baru</span>
    </div>

    <form id="formPermintaan" action="{{ url('permintaan_darah_penyimpanan') }}" method="POST">
        @csrf
        
        <input type="hidden" name="no_permintaan" id="no_permintaan_input">
        
        <div class="card">
            <div class="card-title"><i class="ti ti-file-description"></i> Informasi permintaan</div>
            <div class="meta-grid">
                <div class="meta-item">
                    <label>Nomor permintaan</label>
                    <div class="val mono" id="no_permintaan_display">Memuat...</div>
                </div>
                <div class="meta-item">
                    <label>Tanggal minta *</label>
                    <div class="val">
                        <input type="date" name="tanggal_minta" id="tanggal_minta" required value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="meta-item">
                    <label>Bank darah *</label>
                    <div class="val">
                        <select name="bank_darah_kode" id="bank_darah_kode" required>
                            <option value="">Pilih Bank Darah...</option>
                        </select>
                        <input type="hidden" name="bank_darah_nama" id="bank_darah_nama">
                    </div>
                </div>
                <div class="meta-item">
                    <label>Petugas</label>
                    <div class="val">{{ Auth::user()->name ?? 'Petugas' }}</div>
                </div>
            </div>
        </div>

        <!-- Form tambah rincian kantong -->
        <div class="card">
            <div class="card-title"><i class="ti ti-plus"></i> Tambah rincian kantong</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Jenis darah *</label>
                    <select id="jenis_darah">
                        <option value="">Pilih jenis...</option>
                        <option value="TC Apheresis">TC Apheresis</option>
                        <option value="Whole Blood">Whole Blood</option>
                        <option value="PRC">PRC</option>
                        <option value="FFP">FFP</option>
                        <option value="Trombosit">Trombosit</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Golongan darah *</label>
                    <select id="golongan_darah">
                        <option value="">Pilih...</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="AB">AB</option>
                        <option value="O">O</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Rhesus *</label>
                    <select id="rhesus">
                        <option value="Positif">Positif (+)</option>
                        <option value="Negatif">Negatif (−)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jumlah kantong *</label>
                    <input type="number" id="jumlah_kantong" value="1" min="1" max="99">
                </div>
                <div class="form-group">
                    <label>Jumlah CC</label>
                    <input type="number" id="jumlah_cc" value="200" min="0">
                </div>
                <div class="form-group">
                    <label>Tanggal perlu</label>
                    <input type="date" id="tanggal_perlu" value="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group">
                    <label>No. FPUP</label>
                    <input type="text" id="no_fpup" placeholder="Opsional">
                </div>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-primary" onclick="tambahDetail()">
                    <i class="ti ti-circle-plus"></i> Tambah
                </button>
                <button type="button" class="btn" onclick="resetFormDetail()">
                    <i class="ti ti-refresh"></i> Reset
                </button>
            </div>
        </div>

        <!-- Tabel rincian -->
        <div class="card" style="padding-bottom:0">
            <div class="card-title"><i class="ti ti-list-details"></i> Tabel permintaan darah</div>
            <div class="table-wrap">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Jenis darah</th>
                            <th>Gol. darah</th>
                            <th>Rhesus</th>
                            <th class="text-right">Jumlah</th>
                            <th class="text-right">CC</th>
                            <th>Tgl. perlu</th>
                            <th>No. FPUP</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-detail"></tbody>
                    <tfoot>
                        <tr class="footer-summary">
                            <td colspan="4" class="text-right"><strong>Total:</strong></td>
                            <td class="text-right"><strong id="total_kantong">0</strong></td>
                            <td class="text-right"><strong id="total_cc">0</strong></td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="btn-group" style="justify-content:flex-end;margin-top:1rem">
            <a href="{{ url('permintaan_darah_penyimpanan') }}" class="btn btn-default">
                <i class="ti ti-arrow-left"></i> Kembali
            </a>
            <button type="submit" class="btn btn-save">
                <i class="ti ti-device-floppy"></i> Simpan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
let details = [];
let nextId = 1;

function formatDate(dateStr) {
    if (!dateStr) return '';
    const parts = dateStr.split('-');
    return parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : dateStr;
}

function getGolonganClass(gol) {
    const classes = {
        'A': 'gol-a', 'B': 'gol-b', 'AB': 'gol-ab', 'O': 'gol-o'
    };
    return classes[gol] || 'gol-o';
}

function renderTable() {
    const tbody = document.getElementById('tbody-detail');
    
    if (details.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center">Belum ada rincian</td></tr>';
        document.getElementById('total_kantong').textContent = '0';
        document.getElementById('total_cc').textContent = '0';
        return;
    }
    
    let totalKantong = 0;
    let totalCc = 0;
    
    tbody.innerHTML = details.map((detail, index) => {
        totalKantong += parseInt(detail.jumlah_kantong) || 0;
        totalCc += parseInt(detail.jumlah_cc) || 0;
        
        return `
            <tr>
                <td>${index + 1}</td>
                <td>${detail.jenis_darah}</td>
                <td><span class="goldar-pill ${getGolonganClass(detail.golongan_darah)}">${detail.golongan_darah}</span></td>
                <td class="${detail.rhesus === 'Positif' ? 'rhesus-pos' : 'rhesus-neg'}">${detail.rhesus === 'Positif' ? '+ Positif' : '− Negatif'}</td>
                <td class="text-right">${detail.jumlah_kantong}</td>
                <td class="text-right">${detail.jumlah_cc || 0}</td>
                <td>${detail.tanggal_perlu ? formatDate(detail.tanggal_perlu) : '-'}</td>
                <td>${detail.no_fpup || '-'}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-warning" onclick="editDetail(${detail.id})">
                        <i class="ti ti-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusDetail(${detail.id})">
                        <i class="ti ti-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
    
    document.getElementById('total_kantong').textContent = totalKantong;
    document.getElementById('total_cc').textContent = totalCc;
}

function tambahDetail() {
    const jenis = document.getElementById('jenis_darah').value;
    const golongan = document.getElementById('golongan_darah').value;
    const jumlah = parseInt(document.getElementById('jumlah_kantong').value);
    
    if (!jenis) {
        alert('Jenis darah wajib diisi.');
        return;
    }
    if (!golongan) {
        alert('Golongan darah wajib diisi.');
        return;
    }
    if (!jumlah || jumlah < 1) {
        alert('Jumlah kantong minimal 1.');
        return;
    }
    
    details.push({
        id: nextId++,
        jenis_darah: jenis,
        golongan_darah: golongan,
        rhesus: document.getElementById('rhesus').value,
        jumlah_kantong: jumlah,
        jumlah_cc: parseInt(document.getElementById('jumlah_cc').value) || 0,
        tanggal_perlu: document.getElementById('tanggal_perlu').value,
        no_fpup: document.getElementById('no_fpup').value
    });
    
    renderTable();
    resetFormDetail();
}

function hapusDetail(id) {
    if (confirm('Hapus rincian ini?')) {
        details = details.filter(d => d.id !== id);
        renderTable();
    }
}

function editDetail(id) {
    const detail = details.find(d => d.id === id);
    if (!detail) return;
    
    document.getElementById('jenis_darah').value = detail.jenis_darah;
    document.getElementById('golongan_darah').value = detail.golongan_darah;
    document.getElementById('rhesus').value = detail.rhesus;
    document.getElementById('jumlah_kantong').value = detail.jumlah_kantong;
    document.getElementById('jumlah_cc').value = detail.jumlah_cc;
    document.getElementById('tanggal_perlu').value = detail.tanggal_perlu;
    document.getElementById('no_fpup').value = detail.no_fpup;
    
    hapusDetail(id);
}

function resetFormDetail() {
    document.getElementById('jenis_darah').value = '';
    document.getElementById('golongan_darah').value = '';
    document.getElementById('rhesus').value = 'Positif';
    document.getElementById('jumlah_kantong').value = 1;
    document.getElementById('jumlah_cc').value = 200;
    document.getElementById('tanggal_perlu').value = '{{ date("Y-m-d") }}';
    document.getElementById('no_fpup').value = '';
}

// Handle form submission
document.getElementById('formPermintaan').addEventListener('submit', function(e) {
    if (details.length === 0) {
        e.preventDefault();
        alert('Minimal tambahkan 1 rincian kantong darah.');
        return;
    }
    
    // Add details as JSON
    const detailInput = document.createElement('input');
    detailInput.type = 'hidden';
    detailInput.name = 'detail';
    detailInput.value = JSON.stringify(details.map(d => ({
        jenis_darah: d.jenis_darah,
        golongan_darah: d.golongan_darah,
        rhesus: d.rhesus,
        jumlah_kantong: d.jumlah_kantong,
        jumlah_cc: d.jumlah_cc,
        tanggal_perlu: d.tanggal_perlu,
        no_fpup: d.no_fpup
    })));
    this.appendChild(detailInput);
});

// Load bank darah
async function loadBankDarah() {
    try {
        const response = await fetch('{{ url("permintaan_darah_penyimpanan/search-rs") }}?q=');
        const data = await response.json();
        const select = document.getElementById('bank_darah_kode');
        
        data.forEach(rs => {
            const option = document.createElement('option');
            option.value = rs.kode;
            option.textContent = `${rs.kode} — ${rs.nama}`;
            select.appendChild(option);
        });
    } catch (error) {
        console.error('Error:', error);
    }
}

// Generate nomor permintaan
async function generateNoPermintaan() {
    try {
        const response = await fetch('{{ url("permintaan_darah_penyimpanan/next-no-permintaan") }}');
        const data = await response.json();
        document.getElementById('no_permintaan_display').textContent = data.no_permintaan;
        document.getElementById('no_permintaan_input').value = data.no_permintaan;
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('no_permintaan_display').textContent = 'ERROR';
    }
}

// Bank darah change handler
document.getElementById('bank_darah_kode').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    if (selectedOption.value) {
        document.getElementById('bank_darah_nama').value = selectedOption.text;
    }
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadBankDarah();
    generateNoPermintaan();
    renderTable();
});
</script>
@endpush