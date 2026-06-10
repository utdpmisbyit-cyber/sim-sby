<?php

namespace App\Services;

class MenuService
{
    public array $modules = [
        'master' => [
            'route' => 'master',
            'caption' => 'Master Data',
            'sub' => 'Data referensi & konfigurasi',
            'color' => '#5e6cf7',
            'bg' => '#eef0fe',
            'icon' => '<path d="M10 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>',
        ],
        'aftap' => [
            'route' => 'aftap',
            'caption' => 'Aftap',
            'sub' => 'Pengambilan Darah',

            'color' => '#5e6cf7',
            'bg' => '#eef0fe',
            'icon' => '<path d="M10 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>',
        ],
        'unit' => [
            'route' => 'unit',
            'caption' => 'UTD',
            'sub' => 'Unit Transfusi Darah',
            'color' => '#f6a429',
            'bg' => '#fef5e7',
            'icon' => '<path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/>',
        ],
        'mobil_unit' => [
            'route' => 'mobil_unit',
            'caption' => 'Mobil Unit',
            'sub' => 'Mobil PMI Keliling',
            'color' => '#ef4444',
            'bg' => '#fee2e2',
            'icon' => '<path d="M12 2C9.38 2 7.25 4.13 7.25 6.75c0 3.84 3.25 7.69 4.35 8.97a.52.52 0 0 0 .8 0c1.1-1.28 4.35-5.13 4.35-8.97C16.75 4.13 14.62 2 12 2zm0 6.25a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm1 13.75H11v-2H9v-2h2v-2h2v2h2v2h-2v2z"/>',
        ],
        'kantong_darah' => [
            'route' => 'kantong_darah',
            'caption' => 'Kantong Darah',
            'sub' => 'Penyimpanan dan Permintaan',
            'color' => '#17c3b2',
            'bg' => '#e5f9f7',
            'icon' => '<path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V19z"/>',
        ],
        'serologi' => [
            'route' => 'serologi',
            'caption' => 'Serologi',
            'sub' => 'Pemeriksaan & worksheet',
            'color' => '#f1416c',
            'bg' => '#fee6ec',
            'icon' => '<path d="M6 2v6l2 2-2 2v6h12v-6l-2-2 2-2V2H6zm4 14H8v-2h2v2zm0-8H8V6h2v2zm4 8h-2v-2h2v2zm0-8h-2V6h2v2zm2 4h-8v-2h8v2z"/>',
        ],
        'misc' => [
            'route' => 'misc',
            'caption' => 'Pelayanan',
            'sub' => 'Permintaan & pengiriman darah',
            'color' => '#7239ea',
            'bg' => '#f0e8fd',
            'icon' => '<path d="M20 8h-2.81c-.45-.78-1.07-1.45-1.82-1.96L17 4.41 15.59 3l-2.17 2.17C12.96 5.06 12.49 5 12 5s-.96.06-1.41.17L8.41 3 7 4.41l1.62 1.63C7.88 6.55 7.26 7.22 6.81 8H4v2h2.09c-.05.33-.09.66-.09 1v1H4v2h2v1c0 .34.04.67.09 1H4v2h2.81c1.04 1.79 2.97 3 5.19 3s4.15-1.21 5.19-3H20v-2h-2.09c.05-.33.09-.66.09-1v-1h2v-2h-2v-1c0-.34-.04-.67-.09-1H20V8zm-6 8h-4v-2h4v2zm0-4h-4v-2h4v2z"/>',
        ],
        'gudang' => [
            'route' => 'gudang',
            'caption' => 'Gudang',
            'sub' => 'Pendataan & distribusi kantong',
            'color' => '#f97316',
            'bg' => '#fff7ed',
            'icon' => '<path d="M3 7.5 12 3l9 4.5v9L12 21l-9-4.5v-9zm9 11.2 7-3.5V8.8l-7 3.5v6.4zm-2 0v-6.4l-7-3.5v6.4l7 3.5zm1-8.1 7-3.5L12 4 5 7.1l7 3.5z"/>',
        ],
        'inventory' => [
            'route' => 'inventory',
            'caption' => 'Inventory',
            'sub' => 'Barang, stok & opname',
            'color' => '#50cd89',
            'bg' => '#e6f9f0',
            'icon' => '<path d="M20 6h-2.18c.07-.44.18-.86.18-1.3C18 2.55 15.42 0 12.2 0 9.08 0 6.5 2.55 6.5 5.7c0 .44.11.86.18 1.3H4a2 2 0 0 0-2 2l-.01 11c0 1.1.9 2 2.01 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-7.8-4c1.54 0 2.8 1.26 2.8 2.8 0 1.54-1.26 2.8-2.8 2.8-1.54 0-2.8-1.26-2.8-2.8C9.4 3.26 10.66 2 12.2 2zM20 19H4v-2h16v2zm0-5H4V8h4.22l-1.8 2.72 1.67 1.11L10 9.5l2 3 2-3 1.91 2.33 1.67-1.11L15.78 8H20v6z"/>',
        ],
        'finance' => [
            'route' => 'finance',
            'caption' => 'Keuangan',
            'sub' => 'Kas, anggaran & COA',
            'color' => '#0ea5e9',
            'bg' => '#e0f2fe',
            'icon' => '<path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>',
        ],
    ];
   
    protected static array $master = [
        'master' => ['route' => 'master', 'caption' => 'Dashboard'],

        'sistem' => ['route' => '#', 'caption' => 'Sistem', 'sub_menus' => [
            'hak_akses' => ['route' => 'master.hak_akses.index', 'caption' => 'Hak Akses'],
            'user' => ['route' => 'master.user.index', 'caption' => 'User Program'],
        ]],

        'donor' => ['route' => '#', 'caption' => 'Data Donor', 'sub_menus' => [
            'wilayah' => ['route' => 'master.wilayah.index', 'caption' => 'Wilayah Domisili Donor'],
            'kecamatan' => ['route' => 'master.kecamatan.index', 'caption' => 'Kecamatan Donor'],
            'kewarganegaraan' => ['route' => 'master.kewarganegaraan.index', 'caption' => 'Kewarganegaraan Donor'],
            'pekerjaan' => ['route' => 'master.pekerjaan.index', 'caption' => 'Pekerjaan Donor'],
        ]],

        'kepegawaian' => ['route' => '#', 'caption' => 'Kepegawaian', 'sub_menus' => [
            'jabatan' => ['route' => 'master.jabatan.index', 'caption' => 'Jabatan Petugas'],
            'bagian_petugas' => ['route' => 'master.bagian_petugas.index', 'caption' => 'Bagian Petugas'],
            'cabang' => ['route' => 'master.cabang.index', 'caption' => 'Cabang'],
            'petugas' => ['route' => 'master.petugas.index', 'caption' => 'Petugas'],
            'mobil_unit' => ['route' => 'master.mobil_unit.index', 'caption' => 'Mobil Unit'],
        ]],

        'darah' => ['route' => '#', 'caption' => 'Manajemen Darah', 'sub_menus' => [
            'jenis_kantong' => ['route' => 'master.jenis_kantong.index', 'caption' => 'Jenis Kantong'],
            'tipe_kantong' => ['route' => 'master.tipe_kantong.index', 'caption' => 'Tipe Kantong'],
            'jenis_darah' => ['route' => 'master.jenis_darah.index', 'caption' => 'Jenis Darah'],
            'asal_darah' => ['route' => 'master.asal_darah.index', 'caption' => 'Asal Darah'],
            'tujuan_darah' => ['route' => 'master.tujuan_darah.index', 'caption' => 'Tujuan Darah (Rumah Sakit)'],
            'kelas_tujuan_darah' => ['route' => 'master.kelas_tujuan_darah.index', 'caption' => 'Kelas Tujuan Darah'],
            'bank_darah' => ['route' => 'master.bank_darah.index', 'caption' => 'Bank Darah'],
        ]],

        'rumah_sakit' => ['route' => '#', 'caption' => 'Rumah Sakit', 'sub_menus' => [
            'bagian_rumah_sakit' => ['route' => 'master.bagian_rumah_sakit.index', 'caption' => 'Bagian Rumah Sakit'],
            'kelompok_rumah_sakit' => ['route' => 'master.kelompok_rumah_sakit.index', 'caption' => 'Kelompok Rumah Sakit'],
            'diagnosa' => ['route' => 'master.diagnosa.index', 'caption' => 'Diagnosa'],
        ]],

        'laboratorium' => ['route' => '#', 'caption' => 'Laboratorium', 'sub_menus' => [
            'jenis_periksa_serologi' => ['route' => 'master.jenis_periksa_serologi.index', 'caption' => 'Jenis Periksa Serologi'],
            'metode_serologi' => ['route' => 'master.metode_serologi.index', 'caption' => 'Metode Serologi'],
            'reagen_serologi' => ['route' => 'master.reagen_serologi.index', 'caption' => 'Reagen Serologi'],
        ]],

        'keuangan' => ['route' => '#', 'caption' => 'Keuangan', 'sub_menus' => [
            'jenis_biaya' => ['route' => 'master.jenis_biaya.index', 'caption' => 'Jenis Biaya'],
            'kelompok_biaya' => ['route' => 'master.kelompok_biaya.index', 'caption' => 'Kelompok Biaya'],
            'service_cost' => ['route' => 'master.service_cost.index', 'caption' => 'Service Cost'],
            'biaya_cross_test' => ['route' => 'master.biaya_cross_test.index', 'caption' => 'Biaya Cross Test'],
        ]],

        'inventory' => ['route' => '#', 'caption' => 'Inventaris', 'sub_menus' => [
            'kelompok_barang' => ['route' => 'master.kelompok_barang.index', 'caption' => 'Kelompok Barang'],
            'barang' => ['route' => 'master.barang.index', 'caption' => 'Barang'],
            'supplier' => ['route' => 'master.supplier.index', 'caption' => 'Supplier'],
        ]],

        'operasional' => ['route' => '#', 'caption' => 'Operasional', 'sub_menus' => [
            'program_kerja' => ['route' => 'master.program_kerja.index', 'caption' => 'Program Kerja'],
            'pasien_polisitemi' => ['route' => 'misc.pasien_polisitemi.index', 'caption' => 'Pasien Polisitemi'],
        ]],
    ];
     protected static array $aftap = [
        'dashboard' => ['route' => 'aftap', 'caption' => 'Dashboard'],
        'aftap' => ['route' => 'aftap.aftap.index', 'caption' => 'Aftap'],
        'permintaan_kantong' => ['route' => 'aftap.permintaan_kantong.index', 'caption' => 'Permintaan Kantong'],
        'penerimaan_kantong' => ['route' => 'aftap.penerimaan.index', 'caption' => 'Penerimaan Kantong Darah'],
        'pengembalian_kantong' => ['route' => 'aftap.pengembalian_kantong.index', 'caption' => 'Pengembalian Kantong'],
        'pengiriman_sample' => ['route' => 'aftap.pengiriman_sample.index', 'caption' => 'Pengiriman Sample Sero & Produksi'],
        'riwayat_pengiriman_sample' => ['route' => 'aftap.riwayat_pengiriman_sample', 'caption' => 'Riwayat Pengiriman Sample'],

    ];
    protected static array $unit = [
        'unit' => ['route' => 'unit', 'caption' => 'Dashboard'],
        'donor' => ['route' => 'unit.donor.index', 'caption' => 'Donor'],
        'pendaftaran' => ['route' => 'unit.pendaftaran.index', 'caption' => 'Pendaftaran'],
        'pemeriksaan_kesehatan' => ['route' => 'unit.pemeriksaan_kesehatan.index', 'caption' => 'Pemeriksaan Kesehatan'],
        'pemeriksaan_hb' => ['route' => 'unit.pemeriksaan_hb.index', 'caption' => 'Hermatologi / Pra Lab'],
        
        // 'pengeluaran_kantong_mu' => ['route' => 'unit.pengeluaran_kantong_mu.index', 'caption' => 'Pengeluaran Kantong MU'],
        
    ];
    protected static array $mobil_unit = [
        'mobil_unit' => ['route' => 'mobil_unit', 'caption' => 'Dashboard'],
        'pendaftaran' => ['route' => 'mobil_unit.pendaftaran_mobil.index', 'caption' => 'Pendaftaran Mu'],
        'pemeriksaan_kesehatan' => ['route' => 'mobil_unit.pemeriksaan_mobil.index', 'caption' => 'Pemeriksaan Kesehatan'],
        'pemeriksaan_hb' => ['route' => 'mobil_unit.pemeriksaan_mobil_hb.index', 'caption' => 'Hermatologi / Pra Lab'],
        'aftap' => ['route' => 'mobil_unit.aftap.index', 'caption' => 'Aftap'],
        'permintaan_mobil_unit' => ['route' => 'mobil_unit.permintaan_mobil_unit.index', 'caption' => 'Permintaan Kantong Mu'],


    ];
    protected static array $kantong_darah = [
        'kantong_darah' => ['route' => 'kantong_darah', 'caption' => 'Dashboard'],
        'permintaan_kantong' => ['route' => 'kantong_darah.permintaan_kantong.index', 'caption' => 'Permintaan Kantong'],
        'pengembalian_kantong' => ['route' => 'kantong_darah.pengembalian_kantong.index', 'caption' => 'Pengembalian Kantong'],
        'persediaan_kantong' => ['route' => 'kantong_darah.persediaan_kantong.index', 'caption' => 'Persediaan Kantong'],
        'rencana_produksi' => ['route' => 'kantong_darah.rencana_produksi.index', 'caption' => 'Rencana Produksi'],
        'produksi_rilis' => ['route' => 'kantong_darah.produksi_rilis.index', 'caption' => 'Produksi Rilis'],
        
        'penyimpanan' => ['route' => '#.index', 'caption' => 'penyimpanan', 'sub_menus' => [
            'penerimaan_prolis' => ['route' => 'penyimpanan.penerimaan_prolis.index', 'caption' => 'Penerimaan Prolis'],
            'permintaan_external' => ['route' => 'penyimpanan.permintaan_external.index', 'caption' => 'Permintaan External'],
            'pengiriman_darah_external' => ['route' => 'penyimpanan.pengiriman_darah_external.index', 'caption' => 'Pengiriman Darah External'],
            'pengiriman_bank_darah_internal' => ['route' => 'penyimpanan.pengiriman_bank_darah_internal.index', 'caption' => 'Pengiriman Internal'],
            'penyisihan_darah_rusak' => ['route' => 'penyimpanan.penyisihan_darah_rusak.index', 'caption' => 'Penyisihan darah Rusak'],
            'pengembalian_darah_external' => ['route' => 'penyimpanan.pengembalian_darah_external.index', 'caption' => 'Pengembalian External'],
            'opname_darah' => ['route' => 'penyimpanan.opname_darah.index', 'caption' => 'Opname Darah'],
            'stok_darah' => ['route' => 'penyimpanan.stok_darah.index', 'caption' => 'Informasi Stok Darah'],
            'fraksionasi_darah' => ['route' => 'penyimpanan.fraksionasi_darah.index', 'caption' => 'Fraksionasi Darah'],
       ]],
       
    
    ];

   

    protected static array $serologi = [
        'serologi' => ['route' => 'serologi', 'caption' => 'Dashboard'],
        'transaksi_serologi' => ['route' => 'serologi.transaksi_serologi.index', 'caption' => 'Transaksi Serologi'],
        'permintaan_supplier' => ['route' => 'serologi.permintaan_supplier.index', 'caption' => 'Permintaan Barang'],
        'pengiriman_darah_prolis' => ['route' => 'produksi.pengiriman_darah_prolis.index', 'caption' => 'Pengiriman Darah Prolis'],

    
    ];

    protected static array $misc = [
        'misc' => ['route' => 'misc', 'caption' => 'Dashboard'],
        'permintaan_kantong' => ['route' => 'misc.permintaan_kantong.index', 'caption' => 'Permintaan Kantong'],
    
        'bank_darah' => ['route' => '#.index', 'caption' => 'Bank Darah', 'sub_menus' => [
            'permintaan_fpup' => ['route' => 'unit.bank_darah.permintaan_fpup.index', 'caption' => 'Permintaan Fpup'],
            'pemberian_darah' => ['route' => 'unit.bank_darah.pemberian_darah.index', 'caption' => 'Pemberian Darah'],
            'pelayanan_darah' => ['route' => 'unit.bank_darah.pelayanan_darah.index', 'caption' => 'Pelayanan Darah'],
            'permintaan_darah_penyimpanan' => ['route' => 'unit.permintaan_darah_penyimpanan.index', 'caption' => 'Permintaan Darah Penyimpanan'],
        ]],
        
    ];

    protected static array $gudang = [
        'gudang' => ['route' => 'gudang', 'caption' => 'Dashboard'],
        'pendataan_kantong' => ['route' => 'gudang.pendataan_kantong.index', 'caption' => 'Pendataan Kantong'],
        'stok_kantong' => ['route' => 'gudang.stok_kantong.index', 'caption' => 'Stok Kantong'],
        'pengeluaran_kantong' => ['route' => 'gudang.pengeluaran_kantong.index', 'caption' => 'Pengeluaran Kantong'],
        'cetak_barcode'  => ['route' => 'gudang.cetak_barcode.index', 'caption' => 'Cetak Ulang Barcode'],
    ];

    protected static array $inventory = [
        'inventory' => ['route' => 'inventory', 'caption' => 'Dashboard'],
        'barang' => ['route' => 'inventory.barang.index', 'caption' => 'Barang'],

        'supplier' => ['route' => 'inventory.supplier.index', 'caption' => 'Supplier'],
        'stok' => ['route' => 'inventory.stok.index', 'caption' => 'Stok'],
        'pembelian' => ['route' => '#.index', 'caption' => 'Pembelian', 'sub_menus' => [
            'pengajuan_supplier' => ['route' => 'inventory.pengajuan_supplier.index', 'caption' => 'Pengajuan Supplier'],
            'permintaan_supplier' => ['route' => 'inventory.permintaan_supplier.index', 'caption' => 'Permintaan Supplier'],
            'purchase_order' => ['route' => 'inventory.purchase_order.index', 'caption' => 'Purchase Order'],
            'qc_barang_masuk' => ['route' => 'inventory.qc_barang_masuk.index', 'caption' => 'QC Barang Masuk'],
            'return_supplier' => ['route' => 'inventory.return_supplier.index', 'caption' => 'Return Supplier'],
        ]],
        'pengeluaran' => ['route' => '#.index', 'caption' => 'Pengeluaran', 'sub_menus' => [
            'pengajuan_barang' => ['route' => 'inventory.pengajuan_barang.index', 'caption' => 'Pengajuan Barang'],
            'pengeluaran_barang' => ['route' => 'inventory.pengeluaran_barang.index', 'caption' => 'Pengeluaran Barang'],
            'pemakaian_barang' => ['route' => 'inventory.pemakaian_barang.index', 'caption' => 'Pemakaian Barang'],
            'pinjam_barang' => ['route' => 'inventory.pinjam_barang.index', 'caption' => 'Pinjam Barang'],
            'retur_pinjam' => ['route' => 'inventory.retur_pinjam.index', 'caption' => 'Retur Pinjam'],
        ]],
        'opname_barang' => ['route' => 'inventory.opname_barang.index', 'caption' => 'Opname Barang'],
        'dokumentasi_barang' => ['route' => 'inventory.dokumentasi_barang.index', 'caption' => 'Dokumentasi Barang'],
    ];

    protected static array $finance = [
        'finance' => ['route' => 'finance', 'caption' => 'Dashboard'],
        'coa' => ['route' => 'finance.coa.index', 'caption' => 'Chart of Account'],
        'anggaran' => ['route' => 'finance.anggaran.index', 'caption' => 'Anggaran'],
        'kas' => ['route' => '#.index', 'caption' => 'Kas', 'sub_menus' => [
            'kas_masuk' => ['route' => 'finance.kas_masuk.index', 'caption' => 'Kas Masuk'],
            'kas_keluar' => ['route' => 'finance.kas_keluar.index', 'caption' => 'Kas Keluar'],
        ]],
        'laporan' => ['route' => '#.index', 'caption' => 'Laporan', 'sub_menus' => [
            'trial_balance' => ['route' => 'finance.trial_balance.index', 'caption' => 'Trial Balance'],
            'general_leadge' => ['route' => 'finance.general_leadge.index', 'caption' => 'General Ledger'],
            'penyesuaian' => ['route' => 'finance.penyesuaian.index', 'caption' => 'Penyesuaian'],
        ]],
    ];

    public function listMenu($module): array
    {
        return match ($module) {
            'master' => self::$master,
            'unit' => self::$unit,
            'kantong_darah' => self::$kantong_darah,
            'aftap' => self::$aftap,
            'serologi' => self::$serologi,
            'misc' => self::$misc,
            'gudang' => self::$gudang,
            'inventory' => self::$inventory,
            'finance' => self::$finance,
            'mobil_unit'  => self::$mobil_unit,
            default => [],
        };
    }

    public function currentMenu($menus, $current_route, $role_active, $head_route, $current_route_params = []) {
        $breadcrumbs = [
            ['route' => explode('.', $current_route)[0], 'caption' => $role_active],
            ['route' => $head_route, 'caption' => $this->modules[$head_route]['caption'] ?? ''],
        ];

        $found = [
            'current_menu' => [],
            'current_sub_menu' => [],
            'current_side_menu' => [],
        ];

        $isMatch = fn($item) => ($item['route'] ?? null) === $current_route && ($item['params'] ?? []) === $current_route_params;

        foreach ($menus as $menu) {
            if ($isMatch($menu)) {
                $found['current_menu'] = $menu;
                $breadcrumbs[] = $menu;
                break;
            }

            foreach ($menu['sub_menus'] ?? [] as $sub) {
                if ($isMatch($sub)) {
                    $found['current_menu'] = $menu;
                    $found['current_sub_menu'] = $sub;
                    if ($sub['route'] !== $menu['route']) $breadcrumbs[] = $sub;
                    break 2;
                }

                foreach ($sub['side_menus'] ?? [] as $side) {
                    if ($isMatch($side)) {
                        $found['current_menu'] = $menu;
                        $found['current_sub_menu'] = $sub;
                        $found['current_side_menu'] = $side;
                        $breadcrumbs[] = $sub;
                        $breadcrumbs[] = $side;
                        break 3;
                    }
                }
            }

            foreach ($menu['side_menus'] ?? [] as $side) {
                if ($isMatch($side)) {
                    $found['current_menu'] = $menu;
                    $found['current_side_menu'] = $side;
                    $breadcrumbs[] = $side;
                    break 2;
                }
            }
        }

        if (empty($found['current_menu'])) {
            $parts = explode('.', $current_route);
            $last = end($parts);

            if (in_array($last, ['show', 'create', 'edit'])) {
                $parts[count($parts) - 1] = 'index';
                return $this->currentMenu($menus, implode('.', $parts), $role_active, $head_route, $current_route_params);
            }

            if (count($parts) > 2) {
                array_splice($parts, -2, 1);
                return $this->currentMenu($menus, implode('.', $parts), $role_active, $head_route, $current_route_params);
            }
        }

        $current = $found['current_side_menu'] ?: ($found['current_sub_menu'] ?: $found['current_menu']);

        return array_merge($found, [
            'breadcrumbs' => $breadcrumbs,
            'actions'     => $current['actions'] ?? [],
        ]);
    }

    public function getActiveSideMenu($side_menus, $current_route, $current_route_params = []) {
        $isMatch = fn($item) => ($item['route'] ?? null) === $current_route && ($item['params'] ?? []) === $current_route_params;

        $current_side_menu = [];

        foreach ($side_menus as $side) {
            if ($isMatch($side)) {
                $current_side_menu = $side;
                break;
            }
        }

        return $current_side_menu;
    }
}
