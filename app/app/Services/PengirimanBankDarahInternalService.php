<?php



namespace App\Services;



use App\Models\PengirimanBankDarahInternal;
use App\Models\PengirimanBankDarahInternalDetail;
use App\Models\PermintaanDarahPenyimpanan;
use App\Models\PenerimaanProlisPenyimpanan;
use App\Models\Petugas;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;


class PengirimanBankDarahInternalService

{

    public function getData()

    {
        return PengirimanBankDarahInternal::with(['details' ])
            ->latest()
            ->get();

    }



    public function getPermintaan()
{
    return PermintaanDarahPenyimpanan::query()
        ->select([
            'id',
            'no_permintaan',
            'bank_darah_nama',
            'tanggal_minta',
            'petugas_nama',   
            'status'
        ])
        ->orderByDesc('tanggal_minta')
        ->get();
}



    public function getPermintaanById($id)

    {

        $permintaan =

            PermintaanDarahPenyimpanan::query()

            ->with('details')

            ->findOrFail($id);



        $result = [];



        foreach ($permintaan->details as $detail) {



            // normalisasi rhesus

            $rhesus = $detail->rhesus;



            if ($rhesus == '+') {

                $rhesus = 'Positif';

            }



            if ($rhesus == '-') {

                $rhesus = 'Negatif';

            }



            $stok = PenerimaanProlisPenyimpanan::query()->whereRaw(

                'TRIM(golongan_darah)=?',

                [trim($detail->golongan_darah)]

            )



            ->whereRaw(

                'LOWER(TRIM(jenis_darah)) = ?',

                [strtolower(trim($detail->jenis_darah))]

            )



            ->where(function($q) use ($detail){



                $rh = strtolower(

                    trim($detail->rhesus)

                );



                if(

                    in_array(

                        $rh,

                        ['+', 'positif', 'positive']

                    )

                ){

                    $q->whereIn(

                        'rhesus',

                        ['+', 'Positif', 'POSITIF']

                    );

                } else {



                    $q->whereIn(

                        'rhesus',

                        ['-', 'Negatif', 'NEGATIF']

                    );

                }

            })



            ->whereDate(

                'tgl_expired',

                '>=',

                today()

            )



            ->orderBy('tgl_expired')



            ->take(

                $detail->jumlah_kantong

            )



            ->get([

                'id',

                'no_stok',

                'no_kantong',

                'jenis_darah',

                'golongan_darah',

                'rhesus',

                'tgl_expired',

                'ml',

                'gr',

                'status'

            ]);



        $result[] = [

            'detail_id' => $detail->id,

            'jenis_darah' => $detail->jenis_darah,

            'golongan_darah' => $detail->golongan_darah,

            'rhesus' => $detail->rhesus,

            'jumlah_kantong' =>

                $detail->jumlah_kantong,

            'jumlah_cc' =>

                $detail->jumlah_cc,

            'stok' => $stok

        ];

    }



    return [

        'header' => $permintaan,

        'details' => $result

    ];

}

    public function findById(int $id)
{
    $data = PengirimanBankDarahInternal::with([
        'details'
    ])->findOrFail($id);

    return [
        'id'                  => $data->id,
        'permintaan_id'       => $data->permintaan_darah_penyimpanan_id,
        'no_permintaan'       => $data->no_permintaan,
        'bank_darah_nama'     => $data->bank_darah_nama,
        'tanggal_pengiriman'  => $data->tanggal_pengiriman,
        'petugas_id'          => $data->petugas_id,
        'petugas_nama'        => $data->petugas_nama,
        'keterangan'          => $data->keterangan,

        'details' => $data->details->map(function ($d) {
            return [
                'id'             => $d->stok_id,
                'no_stok'        => $d->no_stok,
                'no_kantong'     => $d->no_kantong,
                'jenis_darah'    => $d->jenis_darah,
                'golongan_darah' => $d->golongan_darah,
                'rhesus'         => $d->rhesus,
                'tgl_expired'    => $d->tgl_expired,
                'ml'             => $d->ml,
                'gr'             => $d->gr,
                'status'         => $d->status,
            ];
        })->values()
    ];
}


    public function store(array $payload)
    {
        DB::beginTransaction();
        try {
            $permintaan = PermintaanDarahPenyimpanan::with('details')
                ->findOrFail($payload['permintaan_id']);

            $petugas = null;

                if (!empty($payload['petugas_id'])) {
                    $petugas = Petugas::find($payload['petugas_id']);
                }

                if (!$petugas) {
                    $petugas = Petugas::where('user_id', Auth::id())->first();
                }
            $header = PengirimanBankDarahInternal::create([
                'permintaan_darah_penyimpanan_id' => $permintaan->id,
                'no_pengiriman'      => $this->generateNo(),
                'no_permintaan'      => $permintaan->no_permintaan,
                'tanggal_pengiriman' => now(),

                'petugas_id'         => $petugas?->id,
                'petugas_kode'       => $petugas?->kode,
                'petugas_nama'       => $petugas?->nama,

                'bank_darah_kode'    => $permintaan->bank_darah_kode,
                'bank_darah_nama'    => $permintaan->bank_darah_nama,
                'status'             => 'selesai',
                'keterangan'         => $payload['keterangan'] ?? null,
                'created_by'         => Auth::id(),
            ]);

            // Gunakan stok_ids dari frontend jika ada
            $stokIds = $payload['stok_ids'] ?? [];

            if (!empty($stokIds)) {
                $stoks = PenerimaanProlisPenyimpanan::whereIn('id', $stokIds)
                    ->where('status', 'tersedia')
                    ->get();

                foreach ($stoks as $s) {
                    // Cari detail permintaan yang cocok
                    $detail = $permintaan->details->first(function ($d) use ($s) {
                        return strtolower(trim($d->jenis_darah)) === strtolower(trim($s->jenis_darah))
                            && trim($d->golongan_darah) === trim($s->golongan_darah);
                    });

                    PengirimanBankDarahInternalDetail::create([
                        'pengiriman_bank_darah_internal_id' => $header->id,
                        'permintaan_detail_id' => $detail?->id,
                        'stok_id'       => $s->id,
                        'no_stok'       => $s->no_stok,
                        'no_kantong'    => $s->no_kantong,
                        'jenis_darah'   => $s->jenis_darah,
                        'golongan_darah'=> $s->golongan_darah,
                        'rhesus'        => $s->rhesus,
                        'tgl_expired'   => $s->tgl_expired,
                        'gr'            => $s->gr,
                        'ml'            => $s->ml,
                        'jumlah'        => $s->jumlah,
                        'skrining'      => $s->skrining,
                        'status'        => 'terkirim',
                    ]);

                    $s->update(['status' => 'terkirim']);
                }
            }

            foreach ($permintaan->details as $detail) {
                $detail->update(['status' => 'selesai']);
            }
            $permintaan->update(['status' => 'selesai']);

            DB::commit();
            return $header;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function cariStokByNoStok(string $noStok)
{
    return PenerimaanProlisPenyimpanan::query()
        ->where('no_stok', $noStok)
        ->where('status', 'tersedia')
        ->whereDate('tgl_expired', '>=', today())
        ->first([
            'id','no_stok','no_kantong','jenis_darah',
            'golongan_darah','rhesus','tgl_expired','ml','gr','status'
        ]);
}

    public function destroy($id)

    {

        DB::beginTransaction();



        try {



            $header = PengirimanBankDarahInternal::with('details')->findOrFail($id);

            foreach ($header->details as $detail) {

                PenerimaanProlisPenyimpanan::query()->where('id', $detail->stok_id)->update(['status'=> 'tersedia']);

            }



            $header->permintaan?->update(['status'=> 'permintaan']);

            $header->delete();

            DB::commit();



        } catch (Exception $e) {



            DB::rollBack();



            throw $e;

        }

    }



    private function generateNo()

    {

        $date = Carbon::now()->format('Y');

        $last = PengirimanBankDarahInternal::query()

                ->whereYear('created_at', now()->year)

                ->latest('id')

                ->first();



        $urut = $last ? ((int) substr(

                $last->no_pengiriman, -6 )) + 1 : 1;



        return 'PKD'. $date . str_pad( $urut,6,'0',STR_PAD_LEFT);

    }

}