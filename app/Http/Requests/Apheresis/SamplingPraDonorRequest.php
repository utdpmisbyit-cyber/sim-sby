<?php

namespace App\Http\Requests\Apheresis;

use Illuminate\Foundation\Http\FormRequest;

class SamplingPraDonorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Ubah semua string kosong ("") menjadi null sebelum divalidasi.
     * Field number (WBC, RBC, PLT, dst) yang dikosongkan di form tetap terkirim
     * sebagai "" sehingga gagal validasi 'numeric' meski rule-nya 'nullable'.
     */
    protected function prepareForValidation(): void
    {
        $cleaned = collect($this->all())->map(function ($value) {
            if (is_array($value)) {
                return $value;
            }
            return $value === '' ? null : $value;
        })->all();

        $this->merge($cleaned);
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'no_transaksi'   => "required|string|max:30|unique:apheresis_sampling_pra_donors,no_transaksi,{$id}",
            'no_donor'       => 'nullable|string|max:30',
            'nama_donor'     => 'required|string|max:150',
            'tgl_lahir'      => 'nullable|date',
            'rhesus'         => 'nullable|in:positif,negatif',
            'jenis_kelamin'  => 'required|in:pria,wanita',
            'golongan_darah' => 'nullable|in:A,B,AB,O',

            'wbc' => 'nullable|numeric', 'neut' => 'nullable|numeric', 'lymph' => 'nullable|numeric',
            'mono' => 'nullable|numeric', 'eo' => 'nullable|numeric', 'baso' => 'nullable|numeric',
            'ig' => 'nullable|numeric',

            'rbc' => 'nullable|numeric', 'hgb' => 'nullable|numeric', 'hct' => 'nullable|numeric',
            'mcv' => 'nullable|numeric', 'mch' => 'nullable|numeric', 'mchc' => 'nullable|numeric',
            'rdw_sd' => 'nullable|numeric', 'rdw_cv' => 'nullable|numeric',

            'plt' => 'nullable|numeric', 'pdw' => 'nullable|numeric', 'mpv' => 'nullable|numeric',
            'p_lcr' => 'nullable|numeric', 'pct' => 'nullable|numeric',

            'status_lulus'        => 'required|in:lulus,tidak_lulus',
            'alasan_tidak_lulus'   => 'required_if:status_lulus,tidak_lulus|array',
            'alasan_tidak_lulus.*' => 'in:lipemik,hb_tinggi,hb_rendah,plt_tinggi,plt_rendah,rbc_tinggi,wbc_tinggi,lain_lain',
            'keterangan'          => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_donor.required'    => 'Nama donor wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'status_lulus.required'  => 'Keterangan lulus/tidak wajib dipilih.',
            'alasan_tidak_lulus.required_if' => 'Alasan tidak lulus wajib dipilih.',
        ];
    }
}