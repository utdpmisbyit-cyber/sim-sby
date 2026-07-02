<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use App\Models\PemberianDarah;
use App\Services\PemberianDarahService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PemberianDarahController extends Controller
{
    public function __construct(protected PemberianDarahService $service) {}

    // ─── Index ───────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = PemberianDarah::with('detail')
            ->orderByDesc('tgl_keluar')
            ->orderByDesc('jam_keluar');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('no_pemberian', 'like', "%$s%")
                  ->orWhere('no_fpup',      'like', "%$s%")
                  ->orWhere('nama_pasien',   'like', "%$s%")
                  ->orWhere('nama_rs',       'like', "%$s%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tgl_dari')) {
            $query->whereDate('tgl_keluar', '>=', $request->tgl_dari);
        }

        if ($request->filled('tgl_sampai')) {
            $query->whereDate('tgl_keluar', '<=', $request->tgl_sampai);
        }

        $list = $query->paginate(15)->withQueryString();

        // ── Stat cards: query terpisah supaya tidak bergantung pada paginator ──
        // ($list->where() tidak bisa dipakai pada LengthAwarePaginator)
        $stats = [
            'hari_ini' => PemberianDarah::whereDate('tgl_keluar', today())->count(),
            'baru'     => PemberianDarah::where('status', 'baru')->count(),
            'selesai'  => PemberianDarah::where('status', 'selesai')->count(),
        ];

        return view('app.unit.bank_darah.pemberian_darah.index', compact('list', 'stats'));
    }

    // ─── Create ──────────────────────────────────────────────────────────────────

    public function create()
    {
        $noPemberian = $this->service->generateNoPemberian();
        return view('app.unit.bank_darah.pemberian_darah.form', compact('noPemberian'));
    }

    // ─── Store ───────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_fpup'               => 'nullable|string|max:30',
            'permintaan_fpup_id'    => 'nullable|integer|exists:permintaan_fpup,id',
            'tgl_keluar'            => 'required|date',
            'jam_keluar'            => 'nullable|date_format:H:i',
            'nama_penerima'         => 'nullable|string|max:100',
            'alamat_penerima'       => 'nullable|string|max:255',
            'nama_pasien'           => 'nullable|string|max:100',
            'nama_dokter'           => 'nullable|string|max:100',
            'nama_rs'               => 'nullable|string|max:100',
            'kode_rs'               => 'nullable|string|max:50',
            'jenis_rs'              => 'nullable|string|max:100',
            'kelas_rawat'           => 'nullable|string|max:100',
            'cara_pembayaran'       => 'nullable|string|max:30',
            'gol_rh_pasien'         => 'nullable|string|max:10',
            'jns_biaya'             => 'nullable|string|max:100',
            'no_reg_online'         => 'nullable|string|max:50',
            'tgl_registrasi_online' => 'nullable|date',
            'petugas'               => 'nullable|string|max:100',
            'kurir_rs'              => 'nullable|string|max:100',
            'pasien_referal'        => 'nullable|boolean',
            'keterangan'            => 'nullable|string',
            'detail'                => 'nullable|array',
            'detail.*.stok_darah_id'  => 'nullable|integer|exists:stok_darah,id',
            'detail.*.no_stok'        => 'nullable|string|max:50',
            'detail.*.jns_darah'      => 'nullable|string|max:50',
            'detail.*.gol'            => 'nullable|string|max:5',
            'detail.*.rhesus'         => 'nullable|string|max:10',
            'detail.*.tgl_expired'    => 'nullable|date',
            'detail.*.metode'         => 'nullable|string|max:30',
            'detail.*.hasil'          => 'nullable|string|max:30',
            'detail.*.jumlah'         => 'nullable|integer|min:1',
            'detail.*.cc'             => 'nullable|integer',
            'detail.*.harga_satuan'   => 'nullable|numeric|min:0',
        ]);

        $pemberian = $this->service->store($validated);

        return redirect()
            ->route('unit.bank_darah.pemberian_darah.show', $pemberian)
            ->with('success', "Pemberian darah {$pemberian->no_pemberian} berhasil disimpan.");
    }

    // ─── Show ────────────────────────────────────────────────────────────────────

    public function show(PemberianDarah $pemberian_darah)
    {
        $pemberian_darah->load('detail.stokDarah', 'permintaanFpup');
        return view('app.unit.bank_darah.pemberian_darah.show', ['pemberian' => $pemberian_darah]);
    }

    // ─── Edit ────────────────────────────────────────────────────────────────────

    public function edit(PemberianDarah $pemberian_darah)
    {
        $pemberian_darah->load('detail');

        // Map detail ke array plain — TIDAK pakai fn() arrow di Blade karena
        // Blade parser error dengan sintaks @json(... fn($d) => [...])
        $detailRows = $pemberian_darah->detail->map(function ($d) {
            return [
                'stok_darah_id' => $d->stok_darah_id,
                'no_stok'       => $d->no_stok,
                'jns_darah'     => $d->jns_darah,
                'gol'           => $d->gol,
                'rhesus'        => $d->rhesus,
                'tgl_expired'   => $d->tgl_expired
                                    ? \Carbon\Carbon::parse($d->tgl_expired)->format('Y-m-d')
                                    : null,
                'metode'        => $d->metode,
                'hasil'         => $d->hasil,
                'jumlah'        => $d->jumlah,
                'cc'            => $d->cc,
                'keterangan'    => $d->keterangan,
                'harga_satuan'  => $d->harga_satuan,
            ];
        })->values()->toArray();

        return view('app.unit.bank_darah.pemberian_darah.edit', [
            'pemberian'  => $pemberian_darah,
            'detailRows' => $detailRows,
        ]);
    }

    // ─── Update ──────────────────────────────────────────────────────────────────

    public function update(Request $request, PemberianDarah $pemberian_darah)
    {
        $validated = $request->validate([
            'no_fpup'               => 'nullable|string|max:30',
            'permintaan_fpup_id'    => 'nullable|integer|exists:permintaan_fpup,id',
            'tgl_keluar'            => 'required|date',
            'jam_keluar'            => 'nullable|date_format:H:i',
            'nama_penerima'         => 'nullable|string|max:100',
            'alamat_penerima'       => 'nullable|string|max:255',
            'nama_pasien'           => 'nullable|string|max:100',
            'nama_dokter'           => 'nullable|string|max:100',
            'nama_rs'               => 'nullable|string|max:100',
            'kode_rs'               => 'nullable|string|max:50',
            'jenis_rs'              => 'nullable|string|max:100',
            'kelas_rawat'           => 'nullable|string|max:100',
            'cara_pembayaran'       => 'nullable|string|max:30',
            'gol_rh_pasien'         => 'nullable|string|max:10',
            'jns_biaya'             => 'nullable|string|max:100',
            'no_reg_online'         => 'nullable|string|max:50',
            'tgl_registrasi_online' => 'nullable|date',
            'petugas'               => 'nullable|string|max:100',
            'kurir_rs'              => 'nullable|string|max:100',
            'pasien_referal'        => 'nullable|boolean',
            'keterangan'            => 'nullable|string',
            'detail'                => 'nullable|array',
            'detail.*.stok_darah_id'  => 'nullable|integer|exists:stok_darah,id',
            'detail.*.no_stok'        => 'nullable|string|max:50',
            'detail.*.jns_darah'      => 'nullable|string|max:50',
            'detail.*.gol'            => 'nullable|string|max:5',
            'detail.*.rhesus'         => 'nullable|string|max:10',
            'detail.*.tgl_expired'    => 'nullable|date',
            'detail.*.metode'         => 'nullable|string|max:30',
            'detail.*.hasil'          => 'nullable|string|max:30',
            'detail.*.jumlah'         => 'nullable|integer|min:1',
            'detail.*.cc'             => 'nullable|integer',
            'detail.*.harga_satuan'   => 'nullable|numeric|min:0',
        ]);

        $this->service->update($pemberian_darah, $validated);

        return redirect()
            ->route('unit.bank_darah.pemberian_darah.show', $pemberian_darah)
            ->with('success', 'Data berhasil diperbarui.');
    }

    // ─── Destroy ─────────────────────────────────────────────────────────────────

    public function destroy(PemberianDarah $pemberian_darah)
    {
        $no = $pemberian_darah->no_pemberian;
        $this->service->destroy($pemberian_darah);
        return redirect()
            ->route('unit.bank_darah.pemberian_darah.index')
            ->with('success', "Pemberian darah $no berhasil dihapus.");
    }

    // ─── API: scan no_fpup ────────────────────────────────────────────────────────

    public function scanFpup(Request $request): JsonResponse
    {
        $request->validate(['no_fpup' => 'required|string']);
        $data = $this->service->getDataByNoFpup($request->no_fpup);
        if (! $data) {
            return response()->json(['message' => 'No FPUP tidak ditemukan.'], 404);
        }
        return response()->json($data);
    }

    // ─── API: scan no_stok ────────────────────────────────────────────────────────

    public function scanStok(Request $request): JsonResponse
    {
        $request->validate(['no_stok' => 'required|string']);
        $data = $this->service->getDataByNoStok($request->no_stok);
        if (! $data) {
            return response()->json(['message' => 'No stok tidak ditemukan atau tidak tersedia.'], 404);
        }
        return response()->json($data);
    }

    // ─── Export Dropping ─────────────────────────────────────────────────────────

    public function exportDropping(PemberianDarah $pemberian_darah): JsonResponse
    {
        $result = $this->service->exportDropping($pemberian_darah);
        return response()->json(['message' => 'Export dropping berhasil.', 'data' => $result]);
    }
}