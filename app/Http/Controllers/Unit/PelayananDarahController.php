<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use App\Models\PelayananDarah;
use App\Services\PelayananDarahService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PelayananDarahController extends Controller
{
    public function __construct(protected PelayananDarahService $service) {}

    // ── Index ─────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $filters      = $request->only(['search', 'status', 'dari', 'sampai', 'per_page']);
        $list         = $this->service->getData($filters);
        // Kirim daftar jenis biaya untuk dropdown di modal create
        $jenisBiayaList = $this->service->getJenisBiayaList();

        return view('app.unit.bank_darah.pelayanan_darah.index',
            compact('list', 'filters', 'jenisBiayaList'));
    }

    // ── Show (JSON untuk modal AJAX) ──────────────────────────────────────────

    public function show(Request $request, PelayananDarah $pelayananDarah)
    {
        // Gunakan relasi details() yang sudah diperbaiki di model
        $pelayananDarah->load('details');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($pelayananDarah);
        }

        return redirect()->route('app.unit.bank_darah.pelayanan_darah.index');
    }

    // ── Store ─────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $data      = $request->validate($this->rules());
        $pelayanan = $this->service->store($data);

        return redirect()
            ->route('app.unit.bank_darah.pelayanan_darah.index')
            ->with('success', "Pelayanan {$pelayanan->no_pelayanan} berhasil disimpan.");
    }

    // ── Update ────────────────────────────────────────────────────────────────

    public function update(Request $request, PelayananDarah $pelayananDarah)
    {
        $data = $request->validate($this->rules($pelayananDarah->id));
        $this->service->update($pelayananDarah, $data);

        return redirect()
            ->route('app.unit.bank_darah.pelayanan_darah.index')
            ->with('success', "Pelayanan {$pelayananDarah->no_pelayanan} berhasil diperbarui.");
    }

    // ── Update Status ─────────────────────────────────────────────────────────

    public function updateStatus(Request $request, PelayananDarah $pelayananDarah)
    {
        $request->validate([
            'status' => ['required', Rule::in(['baru', 'lunas', 'batal'])],
        ]);

        $this->service->updateStatus($pelayananDarah, $request->status);

        return redirect()
            ->route('app.unit.bank_darah.pelayanan_darah.index')
            ->with('success', "Status diubah menjadi {$request->status}.");
    }

    // ── Destroy ───────────────────────────────────────────────────────────────

    public function destroy(PelayananDarah $pelayananDarah)
    {
        $no = $pelayananDarah->no_pelayanan;
        $this->service->destroy($pelayananDarah);

        return redirect()
            ->route('app.unit.bank_darah.pelayanan_darah.index')
            ->with('success', "Pelayanan {$no} berhasil dihapus.");
    }

    // ── API ───────────────────────────────────────────────────────────────────

    public function nextNoPelayanan()
    {
        return response()->json([
            'no_pelayanan' => $this->service->nextNoPelayanan(),
        ]);
    }

    /**
     * Scan no_fpup / no_pemberian → kembalikan data pasien + detail darah + jenis_biaya list.
     * Service sudah mengembalikan array (bukan model), jadi tinggal wrap json.
     */
    public function scanPemberian(Request $request)
    {
        $request->validate(['q' => 'required|string|min:3']);

        $result = $this->service->scanPemberian($request->q);

        if (! $result) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        return response()->json($result);
    }

    /**
     * Endpoint terpisah untuk mengambil daftar jenis_biaya (dipakai modal saat create/edit).
     */
    public function jenisBiayaList()
    {
        return response()->json($this->service->getJenisBiayaList());
    }

    // ── Rules ─────────────────────────────────────────────────────────────────

    private function rules(?int $ignoreId = null): array
    {
        return [
            'pemberian_darah_id'                    => 'nullable|integer|exists:pemberian_darah,id',
            'no_pemberian'                          => 'nullable|string|max:30',
            'no_fpup'                               => 'nullable|string|max:30',
            'tgl_fpup'                              => 'required|date',
            'tgl_pelayanan'                         => 'nullable|date',
            'jam_pelayanan'                         => 'nullable|date_format:H:i',
            'cara_bayar'                            => 'nullable|string|max:20',
            'jns_biaya'                             => 'nullable|string|max:100',
            'no_register'                           => 'nullable|string|max:30',
            'no_faktur'                             => 'nullable|string|max:50',
            'nama_pasien'                           => 'nullable|string|max:100',
            'nama_dokter'                           => 'nullable|string|max:100',
            'nama_rs'                               => 'nullable|string|max:100',
            'kode_rs'                               => 'nullable|string|max:20',
            'jenis_rs'                              => 'nullable|string|max:50',
            'bagian_rs'                             => 'nullable|string|max:100',
            'kelas_rawat'                           => 'nullable|string|max:50',
            'golongan_darah'                        => 'nullable|string|max:10',
            'rhesus'                                => 'nullable|string|max:10',
            'alamat_os'                             => 'nullable|string|max:200',
            'total_biaya'                           => 'nullable|numeric|min:0',
            'diskon'                                => 'nullable|numeric|min:0',
            'total_bayar'                           => 'nullable|numeric|min:0',
            'terbayar'                              => 'nullable|numeric|min:0',
            'kembalian'                             => 'nullable|numeric|min:0',
            'keterangan'                            => 'nullable|string',
            'cara_pembayaran'                       => 'nullable|string|max:30',
            'details'                               => 'nullable|array',
            'details.*.pemberian_darah_detail_id'   => 'nullable|integer',
            'details.*.no_stok'                     => 'nullable|string|max:50',
            'details.*.jns_darah'                   => 'nullable|string|max:50',
            'details.*.gol'                         => 'nullable|string|max:5',
            'details.*.rhesus'                      => 'nullable|string|max:10',
            'details.*.jumlah'                      => 'nullable|integer|min:1',
            'details.*.cc'                          => 'nullable|integer|min:0',
            'details.*.harga_satuan'                => 'nullable|numeric|min:0',
            'details.*.keterangan'                  => 'nullable|string',
        ];
    }
}