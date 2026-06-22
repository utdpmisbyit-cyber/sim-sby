<?php

namespace App\Services;

use App\Models\PermintaanFpupReferal;
use App\Models\PermintaanFpupReferalDetail;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PermintaanFpupReferalService
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = PermintaanFpupReferal::with('details')
            ->orderByDesc('tgl_minta')
            ->orderByDesc('id');

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $query->where(function ($q) use ($s) {
                $q->where('no_fpup',        'like', "%{$s}%")
                  ->orWhere('no_referal',   'like', "%{$s}%")
                  ->orWhere('nama_pasien',  'like', "%{$s}%")
                  ->orWhere('diagnosa_klinis', 'like', "%{$s}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['status_referal'])) {
            $query->where('status_referal', $filters['status_referal']);
        }

        if (!empty($filters['tgl_dari']) && !empty($filters['tgl_sampai'])) {
            $query->whereBetween('tgl_minta', [$filters['tgl_dari'], $filters['tgl_sampai']]);
        }

        if (!empty($filters['jns_permintaan'])) {
            $query->where('jns_permintaan', $filters['jns_permintaan']);
        }

        return $query->paginate($perPage);
    }

    private function generateNoReg(): string
    {
        $prefix = now()->format('ymd');

        $last = PermintaanFpupReferal::whereDate('created_at', today())
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        $urut = $last ? ((int) $last->id + 1) : 1;

        return $prefix . str_pad((string) $urut, 5, '0', STR_PAD_LEFT);
    }

    private function generateNoRegOnline(): string
    {
        return 'ONL-' . now()->format('YmdHis');
    }

    private function generateNoFpup(): string
    {
        $prefix = 'R' . now()->format('ym');

        $last = PermintaanFpupReferal::where('no_fpup', 'like', $prefix . '%')
            ->orderByDesc('no_fpup')
            ->lockForUpdate()
            ->first();

        $urut = 1;

        if ($last) {
            $urut = (int) substr($last->no_fpup, -5) + 1;
        }

        return $prefix . str_pad((string) $urut, 5, '0', STR_PAD_LEFT);
    }

    public function getById(int $id): PermintaanFpupReferal
    {
        return PermintaanFpupReferal::with(['details', 'permintaanFpup'])
            ->findOrFail($id);
    }

    // ─── Statistik ────────────────────────────────────────────────────────────

    public function countAll(): int
    {
        return PermintaanFpupReferal::count();
    }

    public function countByStatus(string $status): int
    {
        return PermintaanFpupReferal::where('status', $status)->count();
    }

    // ─── CRUD ─────────────────────────────────────────────────────────────────

    public function store(array $data): PermintaanFpupReferal
    {
        $data = $this->normalizeData($data);
        $this->validateHeader($data);

        return DB::transaction(function () use ($data) {
            $details = $data['details'] ?? [];
            unset($data['details']);

            // ── Nomor & Registrasi: generate kalau benar-benar kosong ──
            $data['no_referal']             = $data['no_referal'] ?? $this->generateNoReferal();
            $data['no_fpup']                = $data['no_fpup'] ?? $this->generateNoFpup();
            $data['no_reg']                 = $data['no_reg'] ?? $this->generateNoReg();
            $data['no_reg_online']          = $data['no_reg_online'] ?? $this->generateNoRegOnline();
            $data['tgl_referal']            = $data['tgl_referal'] ?? now()->toDateString();
            $data['tgl_registrasi_online']  = $data['tgl_registrasi_online'] ?? now()->toDateString();

            // ── Default value supaya tidak ada kolom penting yang null ──
            $data['pasien_referal']  = $data['pasien_referal'] ?? true;
            $data['status']          = $data['status'] ?? 'baru';
            $data['status_referal']  = $data['status_referal'] ?? 'pending';
            $data['jns_donor']       = $data['jns_donor'] ?? 'Sukarela';
            $data['jml_donor']       = $data['jml_donor'] ?? $this->hitungJmlDonor($details);
            $data['kebangsaan']      = $data['kebangsaan'] ?? 'INDONESIA';
            $data['nama_dokter']     = $data['nama_dokter'] ?? '-';
            $data['nama_os']         = $data['nama_os'] ?? ($data['nama_pasien'] ?? '-');
            $data['alasan_referal']  = $data['alasan_referal'] ?? '-';
            $data['jns_biaya']       = $data['jns_biaya'] ?? '-';

            $referal = PermintaanFpupReferal::create($data);

            if (!empty($details)) {
                $this->syncDetails($referal, $details);
            }

            return $referal->load('details');
        });
    }

    public function update(int $id, array $data): PermintaanFpupReferal
    {
        $referal = $this->getById($id);
        $data    = $this->normalizeData($data);
        $this->validateHeader($data, $id);

        return DB::transaction(function () use ($referal, $data) {
            $details = $data['details'] ?? null;
            unset($data['details']);

            $data = $this->dropNullExceptNullable($data);

            // Jaga-jaga: field unique/penting jangan pernah dikosongkan
            // walau lolos dari filter di atas.
            foreach (['no_fpup', 'no_referal', 'no_reg', 'no_reg_online', 'nama_pasien'] as $protectedKey) {
                if (array_key_exists($protectedKey, $data) && $data[$protectedKey] === null) {
                    unset($data[$protectedKey]);
                }
            }

            if ($details !== null && !array_key_exists('jml_donor', $data)) {
                $data['jml_donor'] = $this->hitungJmlDonor($details);
            }

            if (!empty($data)) {
                $referal->update($data);
            }

            if ($details !== null) {
                $this->syncDetails($referal, $details);
            }

            return $referal->load('details');
        });
    }


    private function dropNullExceptNullable(array $data): array
    {
       
        $freelyNullable = [
            'catatan',
            'reaksi_gejala',
            'serologi_dimana',
            'alasan_transfusi',
            'transfusi_kapan',
            'serologi_kapan',
            'jam_minta',
            'jam_terima',
            'tgl_terima',
            'nama_suami_istri',
            'no_ktp',
            'tgl_lahir',
            'umur',
            'cetak_barcode',
            'hdn',
            'transfusi_sebelumnya',
            'reaksi_transfusi',
            'pernah_serologi',
        ];

        foreach ($data as $key => $value) {
            if ($value !== null) {
                continue;
            }
            if (in_array($key, $freelyNullable, true)) {
                continue;
            }
            // Kolom ini nilainya null & bukan termasuk yang boleh bebas
            // dikosongkan → buang key supaya nilai lama di DB tidak tertimpa.
            unset($data[$key]);
        }

        return $data;
    }

    public function destroy(int $id): bool
    {
        return (bool) $this->getById($id)->delete();
    }

    public function jadikanReferal(int $fpupId, array $extra = []): PermintaanFpupReferal
    {
        $fpup = \App\Models\PermintaanFpup::with('details')->findOrFail($fpupId);

        $existing = PermintaanFpupReferal::where('permintaan_fpup_id', $fpupId)->first();
        if ($existing) {
            throw new \Exception(
                "FPUP #{$fpup->no_fpup} sudah dijadikan referal (No. Referal: {$existing->no_referal})."
            );
        }

        return DB::transaction(function () use ($fpup, $extra) {
            $referalData = collect($fpup->toArray())
                ->except(['id', 'created_at', 'updated_at', 'deleted_at', 'details'])
                ->toArray();

            $referalData['permintaan_fpup_id']  = $fpup->id;
            $referalData['no_referal']           = $this->generateNoReferal();
            $referalData['tgl_referal']          = now()->toDateString();
            $referalData['pasien_referal']       = true;
            $referalData['status_referal']       = 'pending';
            $referalData['alasan_referal']       = $extra['alasan_referal'] ?? ($fpup->alasan_referal ?? '-');
            $referalData['alasan_referal_utama'] = $extra['alasan_referal_utama'] ?? 'Incompatible';
            $referalData['nama_os']              = $referalData['nama_pasien'] ?? '-';
            $referalData['nama_dokter']          = $referalData['nama_dokter'] ?? '-';
            $referalData['kebangsaan']           = $referalData['kebangsaan'] ?? 'INDONESIA';
            $referalData['jns_biaya']            = $referalData['jns_biaya'] ?? '-';

            $referal = PermintaanFpupReferal::create($referalData);

            $jmlDonor = 0;
            foreach ($fpup->details as $detail) {
                $referal->details()->create([
                    'jns_darah'  => $detail->jns_darah,
                    'gol_darah'  => $detail->gol_darah,
                    'rhesus'     => $detail->rhesus,
                    'jumlah'     => $detail->jumlah ?? 1,
                    'cc'         => $detail->cc,
                    'tgl_perlu'  => $detail->tgl_perlu,
                    'keterangan' => $detail->keterangan ?? '-',
                ]);
                $jmlDonor += (int) ($detail->jumlah ?? 1);
            }
            $referal->update(['jml_donor' => $jmlDonor]);

            $fpup->update([
                'pasien_referal' => true,
                'status'         => 'referal',
            ]);

            return $referal->load('details');
        });
    }

    public function updateStatusReferal(int $id, string $statusReferal): PermintaanFpupReferal
    {
        $allowed = ['pending', 'diterima', 'proses', 'selesai', 'ditolak'];

        if (!in_array($statusReferal, $allowed)) {
            throw new \InvalidArgumentException(
                "Status referal '{$statusReferal}' tidak valid. Pilihan: " . implode(', ', $allowed)
            );
        }

        $referal = $this->getById($id);
        $referal->update(['status_referal' => $statusReferal]);

        return $referal;
    }

  
    public function generateNoReferalPublic(): string
    {
        return $this->generateNoReferal();
    }

   
    private function normalizeData(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $trimmed = trim($value);
                $data[$key] = $trimmed === '' ? null : $trimmed;
            }
        }

        // Detail rows: normalisasi tiap baris juga, dan buang baris yang benar-benar kosong
        if (isset($data['details']) && is_array($data['details'])) {
            $data['details'] = array_values(array_filter(array_map(function ($row) {
                if (!is_array($row)) {
                    return null;
                }
                foreach ($row as $k => $v) {
                    if (is_string($v)) {
                        $trimmed = trim($v);
                        $row[$k] = $trimmed === '' ? null : $trimmed;
                    }
                }
                return $row;
            }, $data['details']), function ($row) {
                return $row !== null && !empty($row['jns_darah']);
            }));
        }

        return $data;
    }

    private function hitungJmlDonor(array $details): int
    {
        $total = 0;
        foreach ($details as $d) {
            $total += (int) ($d['jumlah'] ?? 1);
        }
        return $total ?: 1;
    }

    private function syncDetails(PermintaanFpupReferal $referal, array $details): void
    {
       
        $validDetails = array_values(array_filter($details, function ($detail) {
            return is_array($detail) && !empty($detail['jns_darah']);
        }));

        if (empty($validDetails)) {
            return;
        }

        $referal->details()->delete();

        $jmlDonor = 0;

        foreach ($validDetails as $detail) {
            // ── Pastikan tidak ada kolom detail yang kosong tanpa default ──
            $detail['jumlah']     = $detail['jumlah'] ?? 1;
            $detail['gol_darah']  = $detail['gol_darah'] ?? '-';
            $detail['rhesus']     = $detail['rhesus'] ?? '-';
            $detail['keterangan'] = $detail['keterangan'] ?? '-';

            $this->validateDetail($detail);
            $referal->details()->create($detail);

            $jmlDonor += (int) $detail['jumlah'];
        }

        if ($jmlDonor > 0) {
            $referal->update(['jml_donor' => $jmlDonor]);
        }
    }

    private function generateNoReferal(): string
    {
        $prefix = 'REF' . now()->format('Ymd');

        $last = PermintaanFpupReferal::withTrashed()
            ->where('no_referal', 'like', "{$prefix}%")
            ->lockForUpdate()
            ->orderByDesc('no_referal')
            ->value('no_referal');

        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    private function validateHeader(array $data, ?int $ignoreId = null): void
    {
        $uniqueNoFpup    = 'unique:permintaan_fpup_referal,no_fpup';
        $uniqueNoReferal = 'unique:permintaan_fpup_referal,no_referal';
        $uniqueNoKtp     = 'unique:permintaan_fpup_referal,no_ktp';

        if ($ignoreId) {
            $uniqueNoFpup    .= ",{$ignoreId}";
            $uniqueNoReferal .= ",{$ignoreId}";
            $uniqueNoKtp     .= ",{$ignoreId}";
        }

        $rules = [
            'no_fpup'         => ['nullable', 'string', 'max:30', $uniqueNoFpup],
            'no_referal'      => ['nullable', 'string', 'max:30', $uniqueNoReferal],
            'no_ktp'          => ['nullable', 'string', 'max:20', $uniqueNoKtp],
            'tgl_minta'       => ['required', 'date'],
            'nama_pasien'     => ['required', 'string', 'max:100'],
            'jenis_kelamin'   => ['nullable', 'in:Pria,Wanita'],
            'jns_permintaan'  => ['nullable', 'in:CITO,Biasa,Sewaktu'],
            'cara_pembayaran' => ['nullable', 'in:TAGIHAN,TUNAI,BPJS,BAYAR LANGSUNG'],
            'jns_donor'       => ['nullable', 'in:Sukarela,Pengganti'],
            'status_referal'  => ['nullable', 'in:pending,diterima,proses,selesai,ditolak'],
            'details'         => ['sometimes', 'array', 'min:1'],
            'details.*.jns_darah' => ['required_with:details', 'string', 'max:50'],
            'details.*.jumlah'    => ['required_with:details', 'integer', 'min:1'],
        ];

        $messages = [
            'no_ktp.unique'                     => 'No. KTP/NIK sudah terdaftar.',
            'tgl_minta.required'                => 'Tanggal minta wajib diisi.',
            'nama_pasien.required'              => 'Nama pasien wajib diisi.',
            'details.*.jns_darah.required_with' => 'Jenis darah di detail wajib diisi.',
            'details.*.jumlah.required_with'    => 'Jumlah darah di detail wajib diisi.',
        ];

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    private function validateDetail(array $detail): void
    {
        $rules = [
            'jns_darah' => ['required', 'string', 'max:50'],
            'gol_darah' => ['nullable', 'string', 'max:5'],
            'rhesus'    => ['nullable', 'string', 'max:10'],
            'jumlah'    => ['required', 'integer', 'min:1'],
            'cc'        => ['nullable', 'integer', 'min:0'],
            'tgl_perlu' => ['nullable', 'date'],
        ];

        $validator = Validator::make($detail, $rules, [
            'jns_darah.required' => 'Jenis darah wajib diisi.',
            'jumlah.required'    => 'Jumlah wajib diisi.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}