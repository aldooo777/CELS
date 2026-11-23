@extends('layouts.app')
@section('title', 'Data Mahasiswa')
@section('content')
<a href="{{ route('admin.dashboard') }}" class="small">&larr; Dashboard</a>
<h3 class="mt-2 mb-3">Data Mahasiswa <span class="text-muted fs-6">({{ $students->total() }})</span></h3>

<form class="row g-2 mb-3">
    <div class="col-md-4"><input name="q" value="{{ $search }}" class="form-control" placeholder="Cari nama, NIM, atau email"></div>
    <div class="col-md-4">
        <select name="kampus" class="form-select">
            <option value="">Semua kampus</option>
            @foreach ($campuses as $c)
                <option value="{{ $c->id }}" @selected($campusId == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <select name="status" class="form-select">
            <option value="">Semua status</option>
            <option value="terverifikasi" @selected($status === 'terverifikasi')>Terverifikasi</option>
            <option value="belum" @selected($status === 'belum')>Belum verifikasi</option>
        </select>
    </div>
    <div class="col-md-2"><button class="btn btn-primary w-100">Filter</button></div>
</form>

<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Nama</th><th>NIM</th><th>Prodi</th><th>Kampus</th><th>Status</th><th>Pendaftaran</th><th></th></tr></thead>
        <tbody>
        @forelse ($students as $s)
            <tr data-href="{{ route('admin.students.show', $s) }}">
                <td>{{ $s->name }}<br><small class="text-muted">{{ $s->email }}</small></td>
                <td>{{ $s->nim }}</td>
                <td>{{ $s->prodi }}</td>
                <td>{{ $s->campus?->name ?? '-' }}</td>
                <td>
                    @if ($s->email_verified_at)
                        <span class="badge text-bg-success">Terverifikasi</span>
                    @else
                        <span class="badge text-bg-secondary">Belum</span>
                    @endif
                </td>
                <td>{{ $s->applications_count }}</td>
                <td><a href="{{ route('admin.students.show', $s) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada mahasiswa.</td></tr>
        @endforelse
        </tbody>
    </table>
</div></div>
<div class="mt-3">{{ $students->links('pagination::bootstrap-5') }}</div>
@endsection
