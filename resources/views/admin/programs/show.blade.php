@extends('layouts.app')
@section('title', $program->title)
@section('content')
<a href="{{ route('admin.programs.index') }}" class="small">&larr; Data Program</a>
<div class="mt-2 mb-3">
    <h3 class="mb-1">{{ $program->title }}</h3>
    <span class="badge text-bg-primary">{{ $program->typeLabel() }}</span>
    <span class="text-muted small ms-2">{{ $program->campus->name }} · Deadline {{ $program->deadline->translatedFormat('d F Y') }} · Disetujui {{ $approvedCount }}/{{ $program->quota }}</span>
</div>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card"><div class="card-body">
            <h5>Pendaftar ({{ $applications->count() }})</h5>
            <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Nama</th><th>NIM / Prodi</th><th>Tanggal</th><th>Status</th></tr></thead>
                <tbody>
                @forelse ($applications as $a)
                    <tr>
                        <td><a href="{{ route('admin.students.show', $a->student) }}">{{ $a->student->name }}</a></td>
                        <td>{{ $a->student->nim }}<br><small class="text-muted">{{ $a->student->prodi }}</small></td>
                        <td>{{ $a->created_at->format('d/m/Y') }}</td>
                        <td>@include('partials.application-badge', ['application' => $a])</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-3">Belum ada pendaftar.</td></tr>
                @endforelse
                </tbody>
            </table>
            </div>
        </div></div>
    </div>
    <div class="col-lg-4">
        <div class="card"><div class="card-body small">
            <h6>Deskripsi</h6><p class="pre-line">{{ $program->description }}</p>
            <h6>Syarat</h6><p class="pre-line">{{ $program->requirements }}</p>
            @if ($program->partner_info)<h6>Mitra</h6><p class="pre-line">{{ $program->partner_info }}</p>@endif
            <h6>Diposting oleh</h6><p class="mb-0">{{ $program->creator?->name ?? '-' }} · {{ $program->created_at?->translatedFormat('d F Y') }}</p>
        </div></div>
    </div>
</div>
@endsection
