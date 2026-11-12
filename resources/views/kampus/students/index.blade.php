@extends('layouts.app')
@section('title', 'Mahasiswa')
@section('content')
<h3>Mahasiswa {{ auth()->user()->campus->name }}</h3>
<p class="text-muted">Verifikasi manual untuk mahasiswa yang belum bisa memverifikasi lewat email.</p>
<form class="row g-2 mb-3">
    <div class="col-md-3">
        <select name="filter" class="form-select" onchange="this.form.submit()">
            <option value="belum" @selected($filter === 'belum')>Belum terverifikasi</option>
            <option value="sudah" @selected($filter === 'sudah')>Sudah terverifikasi</option>
            <option value="semua" @selected($filter === 'semua')>Semua</option>
        </select>
    </div>
    <div class="col-md-7"><input name="q" value="{{ $search }}" class="form-control" placeholder="Cari nama atau NIM"></div>
    <div class="col-md-2"><button class="btn btn-outline-primary w-100">Cari</button></div>
</form>
<div class="card"><div class="table-responsive">
    <table class="table align-middle mb-0">
        <thead class="table-light"><tr><th>Nama</th><th>NIM</th><th>Prodi</th><th>Email</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse ($students as $s)
            <tr>
                <td>{{ $s->name }}</td><td>{{ $s->nim }}</td><td>{{ $s->prodi }}</td><td>{{ $s->email }}</td>
                <td>
                    @if ($s->email_verified_at)
                        <span class="badge text-bg-success">Terverifikasi</span>
                        @if ($s->verified_by_campus_at)<small class="text-muted d-block">oleh kampus</small>@endif
                    @else
                        <span class="badge text-bg-secondary">Belum</span>
                    @endif
                </td>
                <td>
                    @unless ($s->email_verified_at)
                        <form method="POST" action="{{ route('kampus.students.verify', $s) }}">@csrf
                            <button class="btn btn-sm btn-success">Verifikasi</button>
                        </form>
                    @endunless
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada mahasiswa.</td></tr>
        @endforelse
        </tbody>
    </table>
</div></div>
<div class="mt-3">{{ $students->links('pagination::bootstrap-5') }}</div>
@endsection
