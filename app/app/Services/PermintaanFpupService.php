<?php

namespace App\Services;

use App\Models\Diagnosa;
use App\Models\Fpup;
use App\Models\JenisBiaya;
use App\Models\PermintaanFpup;
use App\Models\PermintaanFpupDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use thiagoalessio\TesseractOCR\TesseractOCR;

class PermintaanFpupService
{
    public function generateNoFpup(): string
    {
        $prefix = 'C' . now()->format('ymd');

        $last = PermintaanFpup::withTrashed()
            ->where('no_fpup', 'like', $prefix . '%')
            ->orderBy('no_fpup', 'desc')
            ->value('no_fpup');

        $seq = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public function store(array $data): PermintaanFpup
    {
        return DB::transaction(function () use ($data) {
            $details = $data['details'] ?? [];
            unset($data['details']);

            $data['no_fpup']   = $this->generateNoFpup();
            $data['tgl_minta'] = now()->toDateString();
            $data['jam_minta'] = now()->format('H:i:s');
            $data['status']    = 'baru';

            $data['no_reg'] = $data['no_reg']
                ?? $this->generateNoRegistrasi();

            $data['no_reg_online'] = $data['no_reg_online']
                ?? $this->generateNoRegistrasiOnline();

            $data['tgl_registrasi_online'] = $data['tgl_registrasi_online']
                ?? now()->toDateString();

            foreach (['transfusi_sebelumnya', 'reaksi_transfusi', 'pernah_serologi', 'hdn', 'pasien_referal', 'cetak_barcode'] as $field) {
                if (array_key_exists($field, $data)) {
                    $data[$field] = filter_var($data[$field], FILTER_VALIDATE_BOOLEAN);
                }
            }

            $fpup = PermintaanFpup::create($data); // $data sudah membawa fpup_id dari modal pasien

            foreach ($details as $d) {
                $fpup->details()->create($d);
            }

            return $fpup->load('details', 'fpup');
        });
    }

    public function update(PermintaanFpup $fpup, array $data): PermintaanFpup
    {
        return DB::transaction(function () use ($fpup, $data) {
            $details = $data['details'] ?? [];
            unset($data['details']);

            foreach (['transfusi_sebelumnya', 'reaksi_transfusi', 'pernah_serologi', 'hdn', 'pasien_referal', 'cetak_barcode'] as $field) {
                if (array_key_exists($field, $data)) {
                    $data[$field] = filter_var($data[$field], FILTER_VALIDATE_BOOLEAN);
                }
            }

            $fpup->update($data);

            $fpup->details()->delete();
            foreach ($details as $d) {
                $fpup->details()->create($d);
            }

            return $fpup->load('details', 'fpup');
        });
    }

    public function destroy(PermintaanFpup $fpup): void
    {
        DB::transaction(fn () => $fpup->delete());
    }

    public function list(array $filters = [])
    {
        $q = PermintaanFpup::with('details', 'fpup')
            ->orderBy('created_at', 'desc');

        if (! empty($filters['search'])) {
            $s = $filters['search'];
            $q->where(function ($qb) use ($s) {
                $qb->where('no_fpup',     'like', "%$s%")
                   ->orWhere('nama_pasien', 'like', "%$s%")
                   ->orWhere('nama_rs',     'like', "%$s%")
                   ->orWhere('no_reg',      'like', "%$s%");
            });
        }

        if (! empty($filters['status'])) {
            $q->where('status', $filters['status']);
        }

        if (! empty($filters['tgl'])) {
            $q->whereDate('tgl_minta', $filters['tgl']);
        }

        return $q->paginate($filters['per_page'] ?? 15);
    }

    public function constants(): array
    {
        $diagnosaDb = Diagnosa::orderBy('nama')->pluck('nama')->toArray();
        $diagnosaList = ! empty($diagnosaDb) ? $diagnosaDb : PermintaanFpup::DIAGNOSA;

        return [
            'jenis_rs'       => PermintaanFpup::JENIS_RS,
            'kategori_rs'    => PermintaanFpup::KATEGORI_RS,
            'bagian'         => PermintaanFpup::BAGIAN,
            'kelas_rawat'    => PermintaanFpup::KELAS_RAWAT,
            'jns_permintaan' => PermintaanFpup::JNS_PERMINTAAN,
            'diagnosa'       => $diagnosaList,
            'cara_bayar'     => PermintaanFpup::CARA_BAYAR,
            'jns_biaya'      => JenisBiaya::orderBy('nama')->pluck('nama')->toArray(),
            'jns_donor'      => PermintaanFpup::JNS_DONOR,
            'gol_darah'      => PermintaanFpup::GOL_DARAH,
            'rhesus'         => PermintaanFpup::RHESUS,
            'jns_darah'      => PermintaanFpup::JNS_DARAH,
            'kebangsaan'     => PermintaanFpup::KEBANGSAAN,
            'status_list'    => PermintaanFpup::STATUS,
        ];
    }

    public function generateNoRegistrasi(): string
    {
        $prefix = 'REG' . now()->format('ymd');

        $last = PermintaanFpup::withTrashed()
            ->where('no_reg', 'like', $prefix . '%')
            ->orderBy('no_reg', 'desc')
            ->value('no_reg');

        $seq = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function generateNoRegistrasiOnline(): string
    {
        $prefix = 'ONL' . now()->format('ymd');

        $last = PermintaanFpup::withTrashed()
            ->where('no_reg_online', 'like', $prefix . '%')
            ->orderBy('no_reg_online', 'desc')
            ->value('no_reg_online');

        $seq = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /* ────────────────────────────────────────────
       MASTER PASIEN (NIK KTP + OCR)
    ──────────────────────────────────────────── */

    /**
     * Validasi format NIK (16 digit) + cross-check tgl lahir & jenis kelamin
     * yang tertanam di digit ke-7 s/d 12 NIK (DDMMYY, DD+40 = perempuan).
     */
    public function validateNik(string $nik, ?string $tglLahir = null, ?string $jenisKelamin = null): array
    {
        if (! preg_match('/^\d{16}$/', $nik)) {
            return ['valid' => false, 'errors' => ['Format NIK harus 16 digit angka.']];
        }

        $dd = (int) substr($nik, 6, 2);
        $mm = (int) substr($nik, 8, 2);
        $yy = (int) substr($nik, 10, 2);

        $isFemaleFromNik = $dd > 40;
        $tgl = $isFemaleFromNik ? $dd - 40 : $dd;

        $errors = [];

        if ($tgl < 1 || $tgl > 31 || $mm < 1 || $mm > 12) {
            $errors[] = 'Kombinasi tanggal lahir pada digit NIK tidak valid.';
        }

        if ($tglLahir && empty($errors)) {
            $birth = Carbon::parse($tglLahir);
            if ((int) $birth->format('d') !== $tgl || (int) $birth->format('m') !== $mm || (int) $birth->format('y') !== $yy) {
                $errors[] = 'Tanggal lahir di form tidak cocok dengan kode tanggal lahir pada NIK.';
            }
        }

        if ($jenisKelamin) {
            $genderNik = $isFemaleFromNik ? 'Wanita' : 'Pria';
            if ($genderNik !== $jenisKelamin) {
                $errors[] = "Jenis kelamin di form ({$jenisKelamin}) tidak cocok dengan kode NIK ({$genderNik}).";
            }
        }

        return [
            'valid'  => empty($errors),
            'errors' => $errors,
            'parsed' => ['jenis_kelamin_nik' => $isFemaleFromNik ? 'Wanita' : 'Pria'],
        ];
    }

    /**
     * Cek apakah NIK sudah pernah terdaftar di master pasien fpup.
     */
    public function checkDuplikatNik(string $nik, ?int $exceptFpupId = null): ?Fpup
    {
        return Fpup::where('no_ktp', $nik)
            ->when($exceptFpupId, fn ($q) => $q->where('id', '!=', $exceptFpupId))
            ->first();
    }

    /**
     * OCR foto KTP pakai Tesseract.
     */
    public function ocrKtp(string $absolutePath): array
    {
        $text = (new TesseractOCR($absolutePath))->lang('ind')->run();

        $nik = null;
        if (preg_match('/NIK\s*[:\-]?\s*(\d{16})/i', $text, $m)) {
            $nik = $m[1];
        } elseif (preg_match('/\b(\d{16})\b/', $text, $m)) {
            $nik = $m[1];
        }

        $nama = null;
        if (preg_match('/Nama\s*[:\-]?\s*([A-Z\' ]{3,})/i', $text, $m)) {
            $nama = trim($m[1]);
        }

        $alamat = null;
        if (preg_match('/Alamat\s*[:\-]?\s*(.+)/i', $text, $m)) {
            $alamat = trim($m[1]);
        }

        return [
            'raw_text' => $text,
            'nik'      => $nik,
            'nama'     => $nama,
            'alamat'   => $alamat,
        ];
    }

    /**
     * Cari pasien di master fpup berdasarkan no_ktp; kalau ada → update,
     * kalau belum ada → buat baru.
     */
    public function findOrCreateFpup(array $data): Fpup
    {
        $pasien = ! empty($data['no_ktp'])
            ? Fpup::where('no_ktp', $data['no_ktp'])->first()
            : null;

        $payload = [
            'nama_pasien'       => $data['nama_pasien'] ?? ($pasien->nama_pasien ?? null),
            'no_ktp'            => $data['no_ktp'] ?? ($pasien->no_ktp ?? null),
            'tgl_lahir'         => $data['tgl_lahir'] ?? ($pasien->tgl_lahir ?? null),
            'umur'              => $data['umur'] ?? ($pasien->umur ?? null),
            'jenis_kelamin'     => $data['jenis_kelamin'] ?? ($pasien->jenis_kelamin ?? null),
            'kebangsaan'        => $data['kebangsaan'] ?? ($pasien->kebangsaan ?? null),
            'no_telp'           => $data['no_telp'] ?? ($pasien->no_telp ?? null),
            'alamat'            => $data['alamat'] ?? ($pasien->alamat ?? null),
            'nama_dokter'       => $data['nama_dokter'] ?? ($pasien->nama_dokter ?? null),
            'nama_instansi'     => $data['nama_instansi'] ?? ($pasien->nama_instansi ?? null),
            'foto_ktp_path'     => $data['foto_ktp_path'] ?? ($pasien->foto_ktp_path ?? null),
            'ocr_raw_result'    => $data['ocr_raw_result'] ?? ($pasien->ocr_raw_result ?? null),
            'ocr_terverifikasi' => $data['ocr_terverifikasi'] ?? ($pasien->ocr_terverifikasi ?? false),
        ];

        $payload['ocr_verified_at'] = $payload['ocr_terverifikasi']
            ? now()
            : ($pasien->ocr_verified_at ?? null);

        $payload['ocr_verified_by'] = $payload['ocr_terverifikasi']
            ? (auth()->user()->name ?? 'system')
            : ($pasien->ocr_verified_by ?? null);

        if ($pasien) {
            $pasien->update($payload);
            return $pasien;
        }

        return Fpup::create($payload);
    }
}