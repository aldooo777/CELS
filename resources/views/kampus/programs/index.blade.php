@extends('layouts.app')
@section('title', 'Program')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Program PDLK</h3>
    <a href="{{ route('kampus.programs.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Posting Program</a>
</div>
<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Judul</th><th>Jenis</th><th>Deadline</th><th>Kuota</th><th>Pendaftar</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse ($programs as $p)
            <tr>
                <td><a href="{{ route('kampus.programs.show', $p) }}" class="fw-semibold">{{ $p->title }}</a></td>
                <td>{{ $p->typeLabel() }}</td>
                <td>{{ $p->deadline->format('d/m/Y') }}</td>
                <td>{{ $p->approved_count }}/{{ $p->quota }}</td>
                <td>{{ $p->applications_count }} @if($p->pending_count)<span class="badge text-bg-warning">{{ $p->pending_count }} perlu ditinjau</span>@endif</td>
                <td>
                    @if (! $p->is_open)<span class="badge text-bg-secondary">Ditutup</span>
                    @elseif ($p->deadline->endOfDay()->isPast())<span class="badge text-bg-secondary">Lewat deadline</span>
                    @else<span class="badge text-bg-success">Dibuka</span>@endif
                </td>
                <td class="text-end"><a href="{{ route('kampus.programs.edit', $p) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a></td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center text-muted py-4">Belum ada program. <a href="{{ route('kampus.programs.create') }}">Posting program pertama</a>.</td></tr>
        @endforelse
        </tbody>
    </table>
</div></div>
<div class="mt-3">{{ $programs->links('pagination::bootstrap-5') }}</div>
@endsection
