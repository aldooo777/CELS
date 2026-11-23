@extends('layouts.app')
@section('title', 'Data Program')
@section('content')
<a href="{{ route('admin.dashboard') }}" class="small">&larr; Dashboard</a>
<h3 class="mt-2 mb-3">Data Program <span class="text-muted fs-6">({{ $programs->total() }})</span></h3>

<form class="row g-2 mb-3">
    <div class="col-md-3"><input name="q" value="{{ $search }}" class="form-control" placeholder="Cari judul program"></div>
    <div class="col-md-3">
        <select name="kampus" class="form-select">
            <option value="">Semua kampus</option>
            @foreach ($campuses as $c)
                <option value="{{ $c->id }}" @selected($campusId == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <select name="jenis" class="form-select">
            <option value="">Semua jenis</option>
            @foreach ($types as $k => $v)
                <option value="{{ $k }}" @selected($type === $k)>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <select name="status" class="form-select">
            <option value="">Semua status</option>
            <option value="dibuka" @selected($status === 'dibuka')>Dibuka</option>
            <option value="ditutup" @selected($status === 'ditutup')>Ditutup</option>
        </select>
    </div>
    <div class="col-md-2"><button class="btn btn-primary w-100">Filter</button></div>
</form>

<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Judul</th><th>Kampus</th><th>Jenis</th><th>Deadline</th><th>Kuota</th><th>Pendaftar</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse ($programs as $p)
            <tr data-href="{{ route('admin.programs.show', $p) }}">
                <td class="fw-semibold">{{ $p->title }}</td>
                <td>{{ $p->campus->name }}</td>
                <td>{{ $p->typeLabel() }}</td>
                <td>{{ $p->deadline->format('d/m/Y') }}</td>
                <td>{{ $p->approved_count }}/{{ $p->quota }}</td>
                <td>{{ $p->applications_count }}</td>
                <td>
                    @if (! $p->is_open)<span class="badge text-bg-secondary">Ditutup</span>
                    @elseif ($p->deadline->endOfDay()->isPast())<span class="badge text-bg-secondary">Lewat deadline</span>
                    @else<span class="badge text-bg-success">Dibuka</span>@endif
                </td>
                <td><a href="{{ route('admin.programs.show', $p) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
            </tr>
        @empty
            <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada program.</td></tr>
        @endforelse
        </tbody>
    </table>
</div></div>
<div class="mt-3">{{ $programs->links('pagination::bootstrap-5') }}</div>
@endsection

