@extends('layouts.index')

@section('title', 'Sampling Pra Donor')

@section('content')
<div class="container-fluid py-3">

    <div class="card shadow-sm border-0">
        <div class="card-header d-flex align-items-center justify-content-between"
             style="background: linear-gradient(90deg,#7a1f2b,#a12d3a); color:#fff;">
            <h5 class="mb-0"><i class="fa fa-vial me-2"></i> Sampling Pra Donor</h5>
            <a href="{{ route('apheresis.sampling_pra_donor.create') }}" class="btn btn-light btn-sm fw-semibold">
                <i class="fa fa-plus me-1"></i> Input Sampling Baru
            </a>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fa fa-search"></i></span>
                        <input type="text" id="q" class="form-control"
                               placeholder="Cari No Transaksi / No Donor / Nama..."
                               value="{{ $q }}">
                    </div>
                </div>
            </div>

            <div class="table-responsive" id="table-wrapper">
                @include('app.apheresis.sampling_pra_donor._table', ['items' => $items])
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const input = document.getElementById('q');
        const wrapper = document.getElementById('table-wrapper');
        let timer = null;

        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(() => {
                fetch(`{{ route('apheresis.sampling_pra_donor.search') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ q: input.value }),
                })
                .then(res => res.json())
                .then(data => { wrapper.innerHTML = data.html; });
            }, 350);
        });
    })();
</script>
@endpush