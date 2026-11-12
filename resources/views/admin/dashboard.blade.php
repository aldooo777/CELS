@extends('layouts.app')
@section('title', 'Dashboard Admin CELS')
@section('content')
<h3 class="mb-4">Dashboard Admin CELS</h3>
<div class="row g-3 mb-4">
    @foreach ([
        ['Menunggu verifikasi', $pendingCount, 'hourglass-split', 'warning', route('admin.registrations.index')],
        ['Kampus uji coba', $trialCount, 'stopwatch', 'info', route('admin.campuses.index', ['status' => 'uji_coba'])],
        ['Kampus berlangganan', $subscribedCount, 'patch-check', 'success', route('admin.campuses.index', ['status' => 'berlangganan'])],
        ['Permintaan demo baru', $demoCount, 'envelope-paper', 'warning', route('admin.demo-requests.index')],
        ['Mahasiswa', $studentCount, 'people', 'primary', null],
        ['Program', $programCount, 'briefcase', 'primary', null],
    ] as [$label, $value, $icon, $color, $link])
        <div class="col-md-4 col-lg-2">
            <div class="card stat-card h-100"><div class="card-body">
                <i class="bi bi-{{ $icon }} text-{{ $color }} fs-4"></i>
                <div class="display-6">{{ $value }}</div>
                <div class="small text-muted">@if($link)<a href="{{ $link }}" class="stretched-link text-reset">{{ $label }}</a>@else{{ $label }}@endif</div>
            </div></div>
        </div>
    @endforeach
</div>

<div class="card"><div class="card-body">
    <h5>Pengajuan terbaru menunggu verifikasi</h5>
    @forelse ($latestPending as $r)
        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
            <div><b>{{ $r->campus->name }}</b><br><small class="text-muted">{{ $r->pic_name }} · {{ $r->created_at->diffForHumans() }}</small></div>
            <a href="{{ route('admin.registrations.show', $r) }}" class="btn btn-sm btn-primary">Periksa</a>
        </div>
    @empty
        <p class="text-muted mb-0">Tidak ada pengajuan yang menunggu.</p>
    @endforelse
</div></div>
@endsection
