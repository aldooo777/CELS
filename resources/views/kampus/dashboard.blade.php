@extends('layouts.app')
@section('title', 'Dashboard Kampus')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-0">{{ $campus->name }}</h3>
        <span class="badge-cels-secondary">{{ $campus->statusLabel() }}</span>
        @if ($campus->status === 'uji_coba' && $campus->trial_ends_at)
            <small class="text-muted">berakhir {{ $campus->trial_ends_at->translatedFormat('d F Y') }} ({{ (int) now()->startOfDay()->diffInDays($campus->trial_ends_at) }} hari lagi)</small>
        @endif
    </div>
</div>

<div class="row g-3 mb-4">
    @foreach ([
        ['Total program', $programCount, 'briefcase', route('kampus.programs.index')],
        ['Program dibuka', $openCount, 'door-open', route('kampus.programs.index')],
        ['Pendaftar baru', $newCount, 'inbox', null],
        ['Disetujui', $approvedCount, 'check2-circle', null],
        ['Mahasiswa belum terverifikasi', $unverifiedCount, 'person-exclamation', route('kampus.students.index')],
    ] as [$label, $value, $icon, $link])
        <div class="col-6 col-lg">
            <div class="card stat-card h-100"><div class="card-body">
                <i class="bi bi-{{ $icon }} text-primary fs-4"></i>
                <div class="display-6">{{ $value }}</div>
                <div class="small text-muted">@if($link)<a href="{{ $link }}" class="stretched-link text-reset">{{ $label }}</a>@else{{ $label }}@endif</div>
            </div></div>
        </div>
    @endforeach
</div>

<div class="card"><div class="card-body">
    <h5>Pendaftaran terbaru</h5>
    <div class="table-responsive">
    <table class="table align-middle mb-0">
        <thead><tr><th>Mahasiswa</th><th>Program</th><th>Tanggal</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse ($latest as $a)
            <tr>
                <td>{{ $a->student->name }}<br><small class="text-muted">{{ $a->student->nim }} · {{ $a->student->prodi }}</small></td>
                <td>{{ $a->program->title }}</td>
                <td>{{ $a->created_at->format('d/m/Y') }}</td>
                <td>@include('partials.application-badge', ['application' => $a])</td>
                <td><a href="{{ route('kampus.applications.show', $a) }}" class="btn btn-sm btn-outline-primary">Periksa</a></td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-muted text-center py-3">Belum ada pendaftaran.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
</div></div>
@endsection
