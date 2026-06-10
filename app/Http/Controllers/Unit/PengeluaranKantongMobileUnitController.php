<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use App\Services\PengeluaranKantongMobileUnitService;
use Illuminate\Http\Request;
use App\Services\PermintaanMobileUnitService;
use Illuminate\Support\Facades\Validator;

class PengeluaranKantongMobileUnitController extends Controller
{
    public function __construct(
        protected PengeluaranKantongMobileUnitService $service
    ) {}

    // ── GET /pengeluaran-kantong-mobile-unit ───────────────────────────────────
    public function index(Request $request)
    {
        $list = $this->service->getPaginatedList($request->only(['search', 'tgl_dari', 'tgl_sampai']));

        return view('app.unit.aftap.pengeluaran_kantong_mu.index', [
            'list'        => $list,
            'mobilUnits'  => $this->service->getMobilUnits(),
            'asalDarahs'  => $this->service->getAsalDarah(),
            'petugasList' => $this->service->getPetugas(),
            'permintaans'   => $this->service->getPermintaanMobileUnit(),
            'kantongItems'=> $this->service->getKantongItems(),
            'nomorKeluar' => \App\Models\PengeluaranKantongMobileUnit::generateNomorKeluar(),
        ]);
    }

   
    public function scanKantong(Request $request)
    {
        $noKantong = trim($request->input('no_kantong', ''));

        if (empty($noKantong)) {
            return response()->json(['success' => false, 'message' => 'No. kantong tidak boleh kosong.'], 422);
        }

        $kantong = $this->service->findKantong($noKantong);

        if (! $kantong) {
            return response()->json(['success' => false, 'message' => 'No. kantong tidak ditemukan di data penerimaan.'], 404);
        }

        if ($kantong['sudah_keluar']) {
            return response()->json(['success' => false, 'message' => 'Kantong ini sudah pernah dikeluarkan.'], 409);
        }

        // Cek duplikat di session saat ini
        $existing = collect($this->service->getKantongItems())
            ->firstWhere('no_kantong', $noKantong);

        if ($existing) {
            return response()->json(['success' => false, 'message' => 'Kantong sudah ada di daftar.'], 409);
        }

        $this->service->addKantongItem($kantong);

        return response()->json([
            'success' => true,
            'message' => 'Kantong berhasil ditambahkan.',
            'item'    => $kantong,
            'items'   => $this->service->getKantongItems(),
        ]);
    }

    // ── DELETE /pengeluaran-kantong-mobile-unit/remove-kantong ────────────────
    public function removeKantong(Request $request)
    {
        $this->service->removeKantongItem($request->input('no_kantong', ''));

        return response()->json([
            'success' => true,
            'items'   => $this->service->getKantongItems(),
        ]);
    }

    // ── POST /pengeluaran-kantong-mobile-unit ─────────────────────────────────
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tgl_keluar'     => 'required|date',
            'permintaan_mobile_unit_id' => 'required|exists:permintaan_mobile_unit,id',
            'mobile_unit_id' => 'required|exists:mobil_unit,id',
            'asal_darah_id'  => 'required|exists:asal_darah,id',
            'petugas_id'     => 'required|exists:petugas,id',
            'tujuan'         => 'nullable|string|max:150',
            'keterangan'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $kantongItems = $this->service->getKantongItems();

        if (empty($kantongItems)) {
            return back()->withErrors(['kantong' => 'Minimal satu kantong harus ditambahkan.'])->withInput();
        }

        $data = $validator->validated();
        $permintaan = \App\Models\PermintaanMobileUnit::find(
            $request->permintaan_mobile_unit_id
        );

        $data['no_permintaan'] = $permintaan?->nomor;


        $data['kantong_items'] = $kantongItems;

        $pengeluaran = $this->service->store($data);

        return redirect()
            ->route('unit.pengeluaran_mobile_unit.index')
            ->with('success', "Pengeluaran {$pengeluaran->no_keluar} berhasil disimpan. Total: " . count($kantongItems) . " kantong.");
    }
        public function edit($id)
    {
        $pengeluaran = $this->service->findById($id);
        
        if (!$pengeluaran) {
            return redirect()->route('unit.pengeluaran_mobile_unit.index')
                ->with('error', 'Data pengeluaran tidak ditemukan.');
        }
        
        // Ambil semua kantong yang terkait dengan pengeluaran ini
        $kantongItems = $this->service->getKantongItemsByPengeluaran($id);
        
        // Set session untuk kantong items yang akan diedit
        $this->service->setEditModeKantongItems($kantongItems);
        
        return view('app.unit.aftap.pengeluaran_kantong_mu.edit', [
            'data'        => $pengeluaran,
            'mobilUnits'  => $this->service->getMobilUnits(),
            'asalDarahs'  => $this->service->getAsalDarah(),
            'petugasList' => $this->service->getPetugas(),
            'permintaans' => $this->service->getPermintaanMobileUnit(),
            'kantongItems'=> $kantongItems,
            'nomorKeluar' => $pengeluaran->no_keluar,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'tgl_keluar'     => 'required|date',
            'permintaan_mobile_unit_id' => 'required|exists:permintaan_mobile_unit,id',
            'mobile_unit_id' => 'required|exists:mobil_unit,id',
            'asal_darah_id'  => 'required|exists:asal_darah,id',
            'petugas_id'     => 'required|exists:petugas,id',
            'tujuan'         => 'nullable|string|max:150',
            'keterangan'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $kantongItems = $this->service->getKantongItems();

        if (empty($kantongItems)) {
            return back()->withErrors(['kantong' => 'Minimal satu kantong harus ditambahkan.'])->withInput();
        }

        $data = $validator->validated();
        $permintaan = \App\Models\PermintaanMobileUnit::find($request->permintaan_mobile_unit_id);
        $data['no_permintaan'] = $permintaan?->nomor;
        $data['kantong_items'] = $kantongItems;

        $pengeluaran = $this->service->update($id, $data);

        return redirect()
            ->route('unit.pengeluaran_mobile_unit.index')
            ->with('success', "Pengeluaran {$pengeluaran->no_keluar} berhasil diupdate. Total: " . count($kantongItems) . " kantong.");
    }

    public function destroy($id)
    {
        try {
            $this->service->delete($id);
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }




}