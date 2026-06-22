<?php

namespace App\Http\Controllers\Referal;

use App\Http\Controllers\Controller;
use App\Models\Fpup;
use App\Services\KtpOcrService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class FpupPasienController extends Controller
{
    public function __construct(
        private readonly KtpOcrService $ocrService
    ) {}

    /**
     * Cari pasien di master tabel `fpup` (autocomplete modal tab "Cari Pasien").
     * GET /referal/permintaan_fpup/pasien/cari?q=...
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));

        if ($q === '') {
            return response()->json(['success' => true, 'data' => []]);
        }

        $results = Fpup::query()
            ->where(function ($query) use ($q) {
                $query->where('nama_pasien', 'like', "%{$q}%")
                      ->orWhere('no_ktp', 'like', "%{$q}%");
            })
            ->orderBy('nama_pasien')
            ->limit(15)
            ->get([
                'id', 'nama_pasien', 'no_ktp', 'tgl_lahir', 'umur',
                'jenis_kelamin', 'kebangsaan', 'no_telp', 'alamat',
                'nama_dokter', 'nama_instansi', 'foto_ktp_path',
                'ocr_terverifikasi',
            ]);

        return response()->json(['success' => true, 'data' => $results]);
    }

    /**
     * Ambil 1 pasien lengkap by id (untuk auto-fill saat dipilih dari hasil cari).
     * GET /referal/permintaan_fpup/pasien/{id}
     */
    public function show(int $id): JsonResponse
    {
        $pasien = Fpup::find($id);

        if (!$pasien) {
            return response()->json(['success' => false, 'message' => 'Pasien tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $pasien]);
    }

    /**
     * Upload foto KTP lalu jalankan OCR, kembalikan hasil parsing untuk
     * ditampilkan sebagai preview di modal (tab "Tambah Baru") SEBELUM disimpan.
     * POST /referal/permintaan_fpup/pasien/ocr-preview
     */
    public function ocrPreview(Request $request): JsonResponse
    {
        $request->validate([
            'foto_ktp' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        try {
            $hasil = $this->ocrService->process($request->file('foto_ktp'));

            $rawTrimmed = trim($hasil['raw_text']);
            $alnumCount = strlen(preg_replace('/[^A-Za-z0-9]/', '', $rawTrimmed));
            $lemah = $alnumCount < 8;

            $message = 'Foto berhasil diproses. Mohon periksa kembali hasil bacaan sebelum disimpan.';

            if ($hasil['glare_detected'] && $lemah) {
                $message = 'Terdeteksi pantulan cahaya (glare) yang cukup besar pada foto, kemungkinan dari laminasi KTP yang mengkilap, sehingga sebagian besar teks gagal terbaca. Coba foto ulang dengan memiringkan KTP sedikit menjauhi sumber cahaya langsung, atau matikan flash, lalu pastikan tidak ada pantulan putih di permukaan KTP sebelum mengambil foto.';
            } elseif ($hasil['glare_detected']) {
                $message = 'Foto berhasil diproses, namun terdeteksi sedikit pantulan cahaya pada sebagian area. Mohon periksa kembali setiap field dengan teliti karena beberapa karakter mungkin kurang akurat.';
            } elseif ($lemah) {
                $message = 'Foto berhasil diunggah, namun hasil pembacaan teks sangat minim. Silakan isi data secara manual, atau coba unggah ulang foto yang lebih jelas, terang, dan tegak lurus.';
            }

            return response()->json([
                'success'        => true,
                'message'        => $message,
                'low_confidence' => $lemah,
                'glare_detected' => $hasil['glare_detected'],
                'foto_url'       => Storage::disk('public')->url($hasil['path']),
                'foto_path'      => $hasil['path'],
                'processed_url'  => $hasil['processed_path']
                    ? Storage::disk('public')->url($hasil['processed_path'])
                    : null,
                'raw_text' => $hasil['raw_text'],
                'parsed'   => $hasil['parsed'],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses OCR: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Simpan pasien baru ke master tabel `fpup` (tab "Tambah Baru" pada modal),
     * setelah user mengonfirmasi/mengoreksi hasil OCR (verifikasi foto manual).
     * POST /referal/permintaan_fpup/pasien
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'nama_pasien'        => ['required', 'string', 'max:100'],
                'no_ktp'             => ['nullable', 'string', 'max:20', 'unique:fpup,no_ktp'],
                'tgl_lahir'          => ['nullable', 'date'],
                'umur'               => ['nullable', 'integer', 'min:0', 'max:200'],
                'jenis_kelamin'      => ['nullable', 'in:Pria,Wanita'],
                'kebangsaan'         => ['nullable', 'string', 'max:50'],
                'no_telp'            => ['nullable', 'string', 'max:20'],
                'alamat'             => ['nullable', 'string'],
                'nama_dokter'        => ['nullable', 'string', 'max:100'],
                'nama_instansi'      => ['nullable', 'string', 'max:100'],
                'keterangan'         => ['nullable', 'string'],
                'foto_ktp_path'      => ['nullable', 'string'],
                'ocr_raw_result'     => ['nullable'],
                'ocr_terverifikasi'  => ['nullable', 'boolean'],
            ]);

            $pasien = DB::transaction(function () use ($data, $request) {
                $data['ocr_terverifikasi'] = (bool) ($data['ocr_terverifikasi'] ?? false);

                if ($data['ocr_terverifikasi']) {
                    $data['ocr_verified_at'] = now();
                    $data['ocr_verified_by'] = $request->user()?->name ?? 'system';
                }

                return Fpup::create($data);
            });

            return response()->json([
                'success' => true,
                'message' => 'Data pasien berhasil disimpan ke master FPUP.',
                'data'    => $pasien,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}