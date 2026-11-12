@extends('layouts.app')
@section('title', $program->title)
@section('content')
<a href="{{ route('kampus.programs.index') }}" class="small">&larr; Semua program</a>
<div class="d-flex justify-content-between align-items-start mt-2 mb-3">
    <div>
        <h3 class="mb-1">{{ $program->title }}</h3>
        <span class="badge text-bg-primary">{{ $program->typeLabel() }}</span>
        <span class="text-muted small ms-2">Deadline {{ $program->deadline->translatedFormat('d F Y') }} · Disetujui {{ $approvedCount }}/{{ $program->quota }}</span>
    </div>
    <a href="{{ route('kampus.programs.edit', $program) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i> Ubah</a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card"><div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">Pendaftar</h5>
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('kampus.programs.show', $program) }}" class="btn btn-outline-secondary @if(!$status) active @endif">Semua</a>
                    @foreach (\App\Models\Application::STATUS_LABELS as $k => $v)
                        <a href="{{ route('kampus.programs.show', [$program, 'status' => $k]) }}" class="btn btn-outline-secondary @if($status === $k) active @endif">{{ $v }}</a>
                    @endforeach
                </div>
            </div>
            <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Nama</th><th>NIM / Prodi</th><th>Tanggal</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @forelse ($applications as $a)
                    <tr>
                        <td>{{ $a->student->name }}</td>
                        <td>{{ $a->student->nim }}<br><small class="text-muted">{{ $a->student->prodi }}</small></td>
                        <td>{{ $a->created_at->format('d/m/Y') }}</td>
                        <td>@include('partials.application-badge', ['application' => $a])</td>
                        <td><a href="{{ route('kampus.applications.show', $a) }}" class="btn btn-sm btn-outline-primary">Periksa</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">Belum ada pendaftar.</td></tr>
                @endforelse
                </tbody>
            </table>
            </div>
            <div class="mt-2">{{ $applications->links('pagination::bootstrap-5') }}</div>
        </div></div>
    </div>
    <div class="col-lg-4">
        <div class="card"><div class="card-body small">
            <h6>Deskripsi</h6><p class="pre-line">{{ $program->description }}</p>
            <h6>Syarat</h6><p class="pre-line">{{ $program->requirements }}</p>
            @if ($program->partner_info)<h6>Mitra</h6><p class="pre-line mb-0">{{ $program->partner_info }}</p>@endif
        </div></div>
    </div>
</div>
@endsection
