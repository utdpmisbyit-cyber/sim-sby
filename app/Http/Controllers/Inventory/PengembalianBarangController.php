<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\PengembalianBarang;
use App\Services\PengembalianBarangService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengembalianBarangController extends Controller
{
    protected PengembalianBarangService $service;

    public function __construct(PengembalianBarangService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $params = $request->only([
            'no_kembali', 'tgl_kembali', 'departemen', 'barang', 'no_kantong', 'kondisi', 'per_page',
        ]);

        $data = $this->service->search($params);

        return view('app.inventory.konfirmasi_pengembalian_barang.index', compact('data', 'params'));
    }

    public function create()
    {
        $no_kembali = $this->service->generateNoKembali();

        return view('app.inventory.konfirmasi_pengembalian_barang.form', compact('no_kembali'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl_kembali'               => 'required|date',
            'departemen'                => 'nullable|string|max:150',
            'keterangan'                => 'nullable|string|max:255',
            'details'                   => 'required|array|min:1',
            'details.*.barang_id'       => 'required|integer|exists:barang,id',
            'details.*.no_kantong'      => 'nullable|string|max:100',
            'details.*.jumlah'          => 'required|integer|min:1',
            'details.*.kondisi'         => 'required|in:baik,rusak',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $pengembalian = PengembalianBarang::create([
                    'no_kembali'  => $this->service->generateNoKembali(),
                    'tgl_kembali' => $request->tgl_kembali,
                    'departemen'  => $request->departemen,
                    'keterangan'  => $request->keterangan,
                    'created_by'  => Auth::id(),
                ]);

                $this->service->processDetails($pengembalian, $request->details);
            });
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['details' => $e->getMessage()]);
        }

        return redirect()
            ->route('inventory.konfirmasi_pengembalian_barang.index')
            ->with('success', 'Konfirmasi pengembalian barang berhasil disimpan.');
    }

    public function show(Request $request, $id)
    {
        $pengembalian = PengembalianBarang::with('details.barang', 'creator')->findOrFail($id);

        if (! $request->ajax()) {
            return redirect()->route('inventory.konfirmasi_pengembalian_barang.edit', $id);
        }

        return response()->json(array_merge(
            $pengembalian->toArray(),
            ['tgl_kembali_fmt' => $pengembalian->tgl_kembali->format('d/m/Y')]
        ));
    }

    public function edit($id)
    {
        $pengembalian = PengembalianBarang::with('details.barang')->findOrFail($id);

        return view('app.inventory.konfirmasi_pengembalian_barang.form', compact('pengembalian'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tgl_kembali'               => 'required|date',
            'departemen'                => 'nullable|string|max:150',
            'keterangan'                => 'nullable|string|max:255',
            'details'                   => 'required|array|min:1',
            'details.*.barang_id'       => 'required|integer|exists:barang,id',
            'details.*.no_kantong'      => 'nullable|string|max:100',
            'details.*.jumlah'          => 'required|integer|min:1',
            'details.*.kondisi'         => 'required|in:baik,rusak',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $pengembalian = PengembalianBarang::with('details')->findOrFail($id);

                // Batalkan efek stok versi lama sebelum menerapkan yang baru.
                $this->service->revertDetails($pengembalian);

                $pengembalian->update([
                    'tgl_kembali' => $request->tgl_kembali,
                    'departemen'  => $request->departemen,
                    'keterangan'  => $request->keterangan,
                ]);

                $this->service->processDetails($pengembalian, $request->details);
            });
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['details' => $e->getMessage()]);
        }

        return redirect()
            ->route('inventory.konfirmasi_pengembalian_barang.index')
            ->with('success', 'Konfirmasi pengembalian barang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $pengembalian = PengembalianBarang::with('details')->findOrFail($id);

            // Balikkan dulu efek penambahan stoknya, baru hapus datanya.
            $this->service->revertDetails($pengembalian);
            $pengembalian->delete();
        });

        return redirect()
            ->route('inventory.konfirmasi_pengembalian_barang.index')
            ->with('success', 'Data berhasil dihapus.');
    }

    /**
     * Endpoint AJAX untuk search dropdown Barang (pola registerSearchDropdown()
     * yang sudah dipakai di modul lain, mis. selectAsalDarah di Aftap).
     * Response format: { results: [{ id, code, text }] }
     */
    public function selectBarang(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $items = Barang::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('nama', 'like', '%' . $q . '%')
                      ->orWhere('kode', 'like', '%' . $q . '%');
            })
            ->orderBy('nama')
            ->limit(30)
            ->get()
            ->map(fn ($b) => [
                'id'   => $b->id,
                'code' => $b->kode,
                'text' => $b->kode . ' - ' . $b->nama,
            ]);

        return response()->json(['results' => $items]);
    }
}