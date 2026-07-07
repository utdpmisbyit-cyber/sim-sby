
@extends('layouts.index')

@section('title', isset($pengembalian) ? 'Edit Pengembalian Barang' : 'Tambah Pengembalian Barang')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css">
@endpush

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ isset($pengembalian) ? 'Edit' : 'Tambah' }} Konfirmasi Pengembalian Barang</h5>
    </div>

    <div class="card-body">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ isset($pengembalian)
                    ? route('inventory.konfirmasi_pengembalian_barang.update', $pengembalian->id)
                    : route('inventory.konfirmasi_pengembalian_barang.store') }}">
            @csrf
            @isset($pengembalian) @method('PUT') @endisset

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label">No. Kembali</label>
                    <input type="text" class="form-control" readonly
                           value="{{ $pengembalian->no_kembali ?? $no_kembali }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tgl Kembali <span class="text-danger">*</span></label>
                    <input type="date" name="tgl_kembali" class="form-control" required
                           value="{{ old('tgl_kembali', isset($pengembalian) ? $pengembalian->tgl_kembali->format('Y-m-d') : now()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Departemen / User</label>
                    <input type="text" name="departemen" class="form-control"
                           placeholder="Contoh: Bagian Laboratorium"
                           value="{{ old('departemen', $pengembalian->departemen ?? '') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Keterangan</label>
                    <input type="text" name="keterangan" class="form-control"
                           value="{{ old('keterangan', $pengembalian->keterangan ?? '') }}">
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Daftar Barang Dikembalikan</h6>
                <button type="button" id="btn-add-row" class="btn btn-sm btn-success">
                    <i class="fa fa-plus"></i> Tambah Baris
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="table-detail">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width:250px">Barang</th>
                            <th style="width:160px">No. Kantong / Serial</th>
                            <th style="width:110px">Jumlah</th>
                            <th style="width:130px">Kondisi</th>
                            <th style="width:50px"></th>
                        </tr>
                    </thead>
                    <tbody id="detail-rows">
                        {{-- Baris di-generate JS. Kalau mode edit, di-seed dari data lama. --}}
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('inventory.konfirmasi_pengembalian_barang.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>

{{-- Template 1 baris detail --}}
<template id="row-template">
    <tr>
        <td>
            <select class="form-select select-barang" name="details[__INDEX__][barang_id]" required></select>
        </td>
        <td>
            <input type="text" class="form-control" name="details[__INDEX__][no_kantong]" placeholder="Opsional">
        </td>
        <td>
            <input type="number" min="1" class="form-control" name="details[__INDEX__][jumlah]" required>
        </td>
        <td>
            <select class="form-select" name="details[__INDEX__][kondisi]" required>
                <option value="baik">Baik</option>
                <option value="rusak">Rusak</option>
            </select>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row">
                <i class="fa fa-times"></i>
            </button>
        </td>
    </tr>
</template>
@endsection

@php
    // Dihitung dulu jadi variabel biasa (BUKAN langsung di dalam @json(...))
    // supaya tidak kena bug Blade @json yang memecah argumen pakai
    // explode(',', ...) dan rusak kalau ekspresinya mengandung banyak koma.
    $existingDetailsForJs = [];

    if (isset($pengembalian)) {
        foreach ($pengembalian->details as $d) {
            $existingDetailsForJs[] = [
                'barang_id'   => $d->barang_id,
                'barang_text' => optional($d->barang)->kode . ' - ' . optional($d->barang)->nama,
                'no_kantong'  => $d->no_kantong,
                'jumlah'      => $d->jumlah,
                'kondisi'     => $d->kondisi,
            ];
        }
    }
@endphp

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
<script>
(function () {
    let rowIndex = 0;
    const tbody         = document.getElementById('detail-rows');
    const rowTemplate    = document.getElementById('row-template').innerHTML;
    const selectBarangUrl = "{{ route('inventory.konfirmasi_pengembalian_barang.select_barang') }}";

    // Data lama untuk mode edit (kalau ada)
    const existingDetails = @json($existingDetailsForJs);

    function initSelect2($el) {
        $($el).select2({
            width: '100%',
            placeholder: 'Cari barang (kode/nama)...',
            ajax: {
                url: selectBarangUrl,
                dataType: 'json',
                delay: 300,
                data: params => ({ q: params.term }),
                processResults: data => ({
                    results: data.results.map(item => ({ id: item.id, text: item.text })),
                }),
            },
        });
    }

    function addRow(prefill) {
        const html = rowTemplate.replaceAll('__INDEX__', rowIndex);
        const wrapper = document.createElement('tbody');
        wrapper.innerHTML = html;
        const tr = wrapper.firstElementChild;
        tbody.appendChild(tr);

        const $select = tr.querySelector('.select-barang');
        initSelect2($select);

        if (prefill) {
            const option = new Option(prefill.barang_text, prefill.barang_id, true, true);
            $select.appendChild(option);
            $($select).trigger('change');

            tr.querySelector('[name$="[no_kantong]"]').value = prefill.no_kantong ?? '';
            tr.querySelector('[name$="[jumlah]"]').value = prefill.jumlah ?? '';
            tr.querySelector('[name$="[kondisi]"]').value = prefill.kondisi ?? 'baik';
        }

        tr.querySelector('.btn-remove-row').addEventListener('click', () => tr.remove());

        rowIndex++;
    }

    document.getElementById('btn-add-row').addEventListener('click', () => addRow(null));

    // Seed baris awal
    if (existingDetails.length > 0) {
        existingDetails.forEach(d => addRow(d));
    } else {
        addRow(null);
    }
})();
</script>
@endpush