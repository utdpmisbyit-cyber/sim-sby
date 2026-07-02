@if(!empty($pemeriksaan_dokters) && count($pemeriksaan_dokters) > 0)

<div class="table-responsive">
    <table class="table table-row-dashed table-row-gray-200 table-sm align-middle">

        <thead>
            <tr class="fs-7 fw-bold bg-secondary text-gray-400 border-bottom-0 text-uppercase">
                <th>No</th>
                <th>Kode Reg.</th>
                <th>Nama Pasien</th>
                <th>Golongan Darah</th>
                <th>Tanggal</th>
                <th>Dokter</th>
                <th>Ruangan</th>
                <th>Status</th>
                <th class="text-end">Aksi</th>
            </tr>
        </thead>

        <tbody>

            <?php $no = 1; ?>

            @foreach($pemeriksaan_dokters as $item)

                <?php

                    $ruanganClass = 'badge-light-secondary';

                    if($item->nomor_ruangan == 1){
                        $ruanganClass = 'badge-light-primary';
                    }
                    elseif($item->nomor_ruangan == 2){
                        $ruanganClass = 'badge-light-info';
                    }
                    elseif($item->nomor_ruangan == 3){
                        $ruanganClass = 'badge-light-danger';
                    }

                ?>

                <tr>

                    <td>{{ $no++ }}</td>

                    <td>
                        {{ $item->kode }}
                    </td>

                    <td>
                        {{ $item->donor->nama ?? '-' }}
                    </td>

                    <td>
                        {{ $item->donor->golongan_darah ?? '-' }}
                        {{ $item->donor->rhesus_caption ?? '' }}
                    </td>

                    <td>
                        {{ formatDate($item->created_at) }}
                        {{ formatTime($item->created_at) }}
                    </td>

                    <td>
                        {{ $item->dokter->nama ?? '-' }}
                    </td>

                    <td>
                        <span class="badge {{ $ruanganClass }}">
                            R{{ $item->nomor_ruangan }}
                        </span>
                    </td>

                    <td>
                        <span class="badge badge-status">
                            {{ $item->status }}
                        </span>
                    </td>

                    <td class="text-end">

                        <button
                            type="button"
                            class="btn btn-sm btn-light-primary"
                            onclick="select_log({{ $item->log_donor_id }})"
                        >
                            Detail
                        </button>

                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>
</div>

@else

<div class="text-center py-10">
    Belum riwayat pemeriksaan hari ini.
</div>

@endif