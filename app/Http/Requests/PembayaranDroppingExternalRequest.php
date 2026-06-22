<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PembayaranDroppingExternalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'jenis_biaya'   => ['nullable', 'string', 'max:50'],
            'harus_dibayar' => ['required', 'numeric', 'min:0'],
            'pembayaran'    => ['required', 'numeric', 'min:0'],
            'metode_bayar'  => ['required', 'in:tunai,kredit'],
            'tanggal_bayar' => ['required', 'date'],
            'keterangan'    => ['nullable', 'string', 'max:1000'],
        ];

        // pengiriman_id hanya wajib & divalidasi unik saat membuat data baru
        if ($this->isMethod('post')) {
            $rules['pengiriman_id'] = ['required', 'integer', 'exists:pengiriman_darah_external,id'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'pengiriman_id.required' => 'Silakan scan / pilih nomor kirim terlebih dahulu.',
            'pengiriman_id.exists'   => 'Data pengiriman tidak ditemukan.',
            'metode_bayar.required'  => 'Pilih metode pembayaran (Tunai/Kredit).',
            'pembayaran.required'    => 'Jumlah pembayaran wajib diisi.',
        ];
    }
}
