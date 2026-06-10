<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermintaanMobileUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bagian_petugas_id'            => 'required|exists:bagian_petugas,id',
            'petugas_id'                   => 'required|exists:petugas,id',
            'verifikator_id'               => 'nullable|exists:petugas,id',
            'keterangan'                   => 'nullable|string',
            'nomor'                        => 'required|string|max:100',
            'tanggal'                      => 'required|date',
            'flag'                         => 'nullable|integer|min:0|max:3',

            'details'                      => 'nullable|array',
            'details.*.id'                 => 'nullable|exists:permintaan_mobile_unit_detail,id',
            'details.*.tipe_kantong_id'    => 'nullable|exists:tipe_kantong,id',
            'details.*.jumlah'             => 'required_with:details|integer|min:1',
            'details.*.jumlah_dilayani'    => 'nullable|integer|min:0',
            'details.*.kode'               => 'nullable|string|max:100',
            'details.*.merk'               => 'nullable|string|max:100',
            'details.*.jenis'              => 'nullable|string|max:100',
            'details.*.ukuran'             => 'nullable|string|max:100',
            'details.*.status'             => 'nullable|string|max:100',
            'details.*.flag'               => 'nullable|integer|min:0',
        ];
    }

    public function attributes(): array
    {
        return [
            'bagian_petugas_id'  => 'Bagian Petugas',
            'petugas_id'         => 'Petugas',
            'verifikator_id'     => 'Verifikator',
            'nomor'              => 'Nomor',
            'tanggal'            => 'Tanggal',
            'details.*.jumlah'   => 'Jumlah Detail',
        ];
    }
}