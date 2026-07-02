<?php

namespace App\Services;

use App\Models\PermintaanAftap;

class PermintaanAftapService extends IoService
{
    public array $status = PermintaanAftap::STATUS;
    public array $merk_kantong = PermintaanAftap::MERK_KANTONG;
    public array $jenis_kantong = PermintaanAftap::JENIS_KANTONG;
    public array $ukuran_kantong = PermintaanAftap::UKURAN;
    
    public function __construct()
    {
        $this->model   = new PermintaanAftap();
        $this->sort_by = ['created_at' => 'desc'];
        $this->filters = ['kode', 'status'];
        $this->fields  = ['kode', 'merk', 'jenis', 'ukuran', 'jumlah', 'status'];
    }
    public function generateNo(): string
    {
        $now = now();
        $yy = $now->format('y');
        $mm = $now->format('m');

        $last = PermintaanAftap::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->orderBy('id', 'desc')
            ->first();

        $seq = $last ? intval(substr($last->kode, -5)) + 1 : 1;
        $seq = str_pad($seq, 5, '0', STR_PAD_LEFT);

        return "PKA-{$yy}{$mm}{$seq}";
    }
    public function getAll()
    {
        return $this->model
            ->orderBy('created_at', 'DESC')
            ->get();
           
    }
   public function store($params) {
    $items = $params['items'] ?? [];

    $results = [];

    foreach ($items as $item) {
        $results[] = PermintaanAftap::create([
            'kode'          => $params['kode'],            // kode permintaan utama
            'tanggal_minta' => $params['tanggal_minta'],
            'merk'          => $item['merk'] ?? null,
            'jenis'         => $item['jenis'] ?? null,
            'ukuran'        => $item['ukuran'] ?? null,
            'jumlah'        => $item['jumlah'] ?? 0,
            'status'        => 'PENDING'
        ]);
    }

    return $results;
}

    public function update($id, $params)
    {
        $data = (array) $params;
        $permintaan = PermintaanAftap::findOrFail($id);
        $permintaan->update([
          'kode' => $data['kode'],
            'merk' => $data['merk'] ?? $permintaan->merk,
            'jenis' => $data['jenis'] ?? $permintaan->jenis,
            'ukuran' => $data['ukuran'] ?? $permintaan->ukuran,
            'jumlah' => $data['jumlah'] ?? $permintaan->jumlah,
            'status' => $data['status'] ?? $permintaan->status,
        ]);
        return $permintaan;
    }

    public function destroy($id)
    {
        $permintaan = PermintaanAftap::findOrFail($id);
        $permintaan->delete();
        return true;
    }
    
}