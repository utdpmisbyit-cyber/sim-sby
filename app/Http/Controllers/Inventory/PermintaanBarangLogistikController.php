<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\IoResourceController;
use App\Models\PengajuanBarang;
use App\Models\Petugas;
use App\Services\PermintaanBarangLogistikService;
use Illuminate\Http\Request;

class PermintaanBarangLogistikController extends IoResourceController
{
    protected $viewPrefix = 'app.inventory.permintaan_barang_logistik';
    protected $itemVariable = 'permintaan';

    public function __construct()
    {
        $this->service = new PermintaanBarangLogistikService();
    }

    public function create()
    {
        return view("{$this->viewPrefix}._form", [
            'pengajuan_options' => $this->getPengajuanOptions(),
            'petugas_options'   => $this->getPetugasOptions(),
        ]);
    }

    public function edit($id)
    {
        $permintaan = $this->service->find($id);

        return view("{$this->viewPrefix}._form", [
            $this->itemVariable => $permintaan,
            'pengajuan_options' => $this->getPengajuanOptions($permintaan->pengajuan_barang_id ?? null),
            'petugas_options'   => $this->getPetugasOptions(),
        ]);
    }

    // dropdown: pengajuan barang yg belum diproses + (kalau edit) pengajuan yg sedang dipakai record ini
    private function getPengajuanOptions($includeId = null)
    {
        $query = PengajuanBarang::with('cabang')
            ->where('status', '!=', 3) // bukan batal
            ->where(function ($q) use ($includeId) {
                $q->whereDoesntHave('permintaanLogistik');
                if ($includeId) {
                    $q->orWhere('id', $includeId);
                }
            })
            ->orderByDesc('tgl_pengajuan');

        return $query->get()->mapWithKeys(function ($item) {
            $label = $item->kode . ' - ' . $item->nama_barang . ' (' . ($item->cabang->nama ?? '-') . ')';
            return [$item->id => $label];
        })->toArray();
    }

    private function getPetugasOptions()
    {
        return Petugas::orderBy('nama')->pluck('nama', 'id')->toArray();
    }

    // tetap dipertahankan untuk kebutuhan lain / future use
    public function getPengajuan($id)
    {
        $pengajuan = PengajuanBarang::with(['cabang', 'petugas', 'barang'])->findOrFail($id);
        return response()->json($pengajuan);
    }

    public function findPengajuan(Request $request)
    {
        $q = $request->get('q');

        $data = PengajuanBarang::whereDoesntHave('permintaanLogistik')
            ->where('status', '!=', 3)
            ->when($q, function ($query) use ($q) {
                $query->where(function ($s) use ($q) {
                    $s->where('kode', 'like', "%{$q}%")
                      ->orWhere('nama_barang', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('tgl_pengajuan')
            ->limit(20)
            ->get();

        return response()->json($data);
    }
}