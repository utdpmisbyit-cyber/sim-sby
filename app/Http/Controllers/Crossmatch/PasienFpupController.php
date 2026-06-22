<?php

namespace App\Http\Controllers\Crossmatch;

use App\Http\Controllers\Controller;
use App\Models\Fpup;
use App\Services\PermintaanFpupService;
use Illuminate\Http\Request;

class PasienFpupController extends Controller
{
    protected PermintaanFpupService $service;

    public function __construct()
    {
        $this->service = new PermintaanFpupService();
    }

    public function cari(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (strlen($q) < 3) {
            return response()->json(['data' => []]);
        }

        $data = Fpup::where('nama_pasien', 'like', "%{$q}%")
            ->orWhere('no_ktp', 'like', "%{$q}%")
            ->orderBy('nama_pasien')
            ->limit(20)
            ->get([
                'id', 'nama_pasien', 'no_ktp', 'jenis_kelamin', 'alamat',
                'tgl_lahir', 'umur', 'kebangsaan', 'no_telp',
                'foto_ktp_path', 'ocr_terverifikasi',
            ]);

        return response()->json(['data' => $data]);
    }

    public function show($id)
    {
        $pasien = Fpup::find($id);

        if (! $pasien) {
            return response()->json(['success' => false, 'message' => 'Pasien tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $pasien]);
    }

    public function ocrPreview(Request $request)
    {
        $request->validate([
            'foto_ktp' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $path = $request->file('foto_ktp')->store('ktp', 'public');

        try {
            $hasil = $this->service->ocrKtp(storage_path('app/public/' . $path));
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses OCR: ' . $e->getMessage(),
            ], 500);
        }

        $lowConfidence = empty($hasil['nik']) || empty($hasil['nama']);

        return response()->json([
            'success'        => true,
            'foto_path'      => $path,
            'raw_text'       => $hasil['raw_text'],
            'parsed'         => [
                'nama_pasien'   => $hasil['nama'],
                'no_ktp'        => $hasil['nik'],
                'alamat'        => $hasil['alamat'],
                'kebangsaan'    => 'INDONESIA',
            ],
            'low_confidence' => $lowConfidence,
            'message'        => $lowConfidence
                ? 'Sebagian data tidak terbaca jelas. Mohon lengkapi/koreksi manual sebelum menyimpan.'
                : 'OCR berhasil membaca data KTP. Mohon periksa kembali sebelum menyimpan.',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pasien'       => 'required|string|max:100',
            'no_ktp'            => 'nullable|digits:16',
            'tgl_lahir'         => 'nullable|date',
            'umur'              => 'nullable|integer',
            'jenis_kelamin'     => 'nullable|string',
            'kebangsaan'        => 'nullable|string',
            'no_telp'           => 'nullable|string|max:20',
            'alamat'            => 'nullable|string',
            'nama_dokter'       => 'nullable|string|max:100',
            'nama_instansi'     => 'nullable|string|max:100',
            'foto_ktp_path'     => 'nullable|string',
            'ocr_raw_result'    => 'nullable|string',
            'ocr_terverifikasi' => 'nullable|boolean',
        ]);

        if (! empty($validated['no_ktp'])) {
            $duplikat = $this->service->checkDuplikatNik($validated['no_ktp']);
            if ($duplikat) {
                return response()->json([
                    'success' => false,
                    'message' => "NIK sudah terdaftar atas nama \"{$duplikat->nama_pasien}\". Gunakan tab Cari Pasien untuk memilih data tersebut.",
                ], 422);
            }
        }

        $pasien = $this->service->findOrCreateFpup($validated);

        return response()->json(['success' => true, 'data' => $pasien]);
    }
}