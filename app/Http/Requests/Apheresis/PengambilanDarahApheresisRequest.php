<?php

namespace App\Http\Requests\Apheresis;

use Illuminate\Foundation\Http\FormRequest;

class PengambilanDarahApheresisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Ubah semua string kosong ("") menjadi null sebelum divalidasi.
     * Perlu karena rule 'nullable' di Laravel hanya mengizinkan nilai NULL,
     * bukan string kosong - field number/date yang dikosongkan di form tetap
     * terkirim sebagai "" sehingga gagal validasi (mis. 'numeric', 'date').
     */
    protected function prepareForValidation(): void
    {
        $cleaned = collect($this->all())->map(function ($value) {
            if (is_array($value)) {
                return collect($value)->map(function ($row) {
                    if (is_array($row)) {
                        return collect($row)->map(fn ($v) => $v === '' ? null : $v)->all();
                    }
                    return $row === '' ? null : $row;
                })->all();
            }
            return $value === '' ? null : $value;
        })->all();

        $this->merge($cleaned);
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'no_transaksi'  => "required|string|max:30|unique:apheresis_pengambilan_darah,no_transaksi,{$id}",
            'petugas_id'    => 'nullable|integer',
            'no_sampling'   => 'nullable|string|max:30',
            'no_donor'      => 'nullable|string|max:30',
            'nama_donor'    => 'required|string|max:150',
            'tgl_lahir'     => 'nullable|date',

            'type_mesin'          => 'nullable|string|max:100',
            'no_mesin'            => 'nullable|string|max:100',
            'operator'            => 'nullable|string|max:100',
            'kode_disposable_kit' => 'nullable|string|max:100',
            'type_ac_ratio'       => 'nullable|string|max:50',
            'cairan_saline'       => 'nullable|string|max:100',

            'no_lot_1' => 'nullable|string|max:50', 'kadaluarsa_lot_1' => 'nullable|date',
            'no_lot_2' => 'nullable|string|max:50', 'kadaluarsa_lot_2' => 'nullable|date',
            'no_lot_3' => 'nullable|string|max:50', 'kadaluarsa_lot_3' => 'nullable|date',

            'golongan_darah' => 'nullable|in:A,B,AB,O',
            'rhesus'         => 'nullable|in:positif,negatif',
            'jenis_kelamin'  => 'required|in:pria,wanita',

            'riwayat_donor_sebelumnya'      => 'nullable|in:pernah,tidak_pernah',
            'riwayat_donor_sebelumnya_kali' => 'nullable|integer|min:0',
            'riwayat_donor_apheresis'       => 'nullable|in:pernah,tidak_pernah',
            'riwayat_donor_apheresis_kali'  => 'nullable|integer|min:0',

            'tinggi_badan'          => 'nullable|numeric',
            'berat_badan'           => 'nullable|numeric',
            'hct'                   => 'nullable|numeric',
            'platelet_precount'     => 'nullable|numeric',
            'target_vol_plasma'     => 'nullable|numeric',
            'target_platelet_yield' => 'nullable|numeric',
            'target_cycle'          => 'nullable|integer|min:0',
            'target_waktu'          => 'nullable|string|max:50',
            'estimasi_vol_plt'      => 'nullable|numeric',

            'waktu_mulai'    => 'nullable|date_format:H:i',
            'waktu_selesai'  => 'nullable|date_format:H:i',
            'durasi'         => 'nullable|string|max:50',
            'vol_wb_terproses'    => 'nullable|numeric',
            'vol_ac_terpakai'     => 'nullable|numeric',
            'vol_saline_terpakai' => 'nullable|numeric',
            'draw_rate'      => 'nullable|numeric',
            'return_rate'    => 'nullable|numeric',
            'plt_hct_postcount' => 'nullable|numeric',

            'platelet_total_vol_aktual'   => 'nullable|numeric',
            'platelet_vol_plt'            => 'nullable|numeric',
            'platelet_vol_plasma_dlm_plt' => 'nullable|numeric',
            'platelet_ac_dlm_plt'         => 'nullable|numeric',
            'platelet_yield_plt'          => 'nullable|numeric',

            'plasma_total_vol_aktual' => 'nullable|numeric',
            'plasma_vol_plasma'       => 'nullable|numeric',
            'plasma_ac_dlm_plasma'    => 'nullable|numeric',

            'catatan'        => 'nullable|string',
            'operator_akhir' => 'nullable|string|max:100',

            'siklus'                     => 'nullable|array',
            'siklus.*.siklus_ke'         => 'nullable|integer|min:1',
            'siklus.*.jam'               => 'nullable|date_format:H:i',
            'siklus.*.draw_return_ml'    => 'nullable|numeric',
            'siklus.*.draw_return_menit' => 'nullable|numeric',
            'siklus.*.plasma_vol'        => 'nullable|numeric',
            'siklus.*.platelet_yield'    => 'nullable|numeric',
            'siklus.*.plasma_vol_2'      => 'nullable|numeric',
            'siklus.*.nacl_sitrat'       => 'nullable|numeric',
            'siklus.*.keterangan'        => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_donor.required'    => 'Nama donor wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
        ];
    }
}