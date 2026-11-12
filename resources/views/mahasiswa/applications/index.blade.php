@extends('layouts.app')
@section('title', 'Pendaftaran Saya')
@section('content')
<h3 class="mb-3">Pendaftaran Saya</h3>
<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Program</th><th>Jenis</th><th>Tanggal daftar</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse ($applications as $a)
            <tr>
                <td>{{ $a->program->title }}</td>
                <td>{{ $a->program->typeLabel() }}</td>
                <td>{{ $a->created_at->format('d/m/Y') }}</td>
                <td>@include('partials.application-badge', ['application' => $a])</td>
                <td><a href="{{ route('mahasiswa.applications.show', $a) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pendaftaran. <a href="{{ route('mahasiswa.programs.index') }}">Cari program</a>.</td></tr>
        @endforelse
        </tbody>
    </table>
</div></div>
<div class="mt-3">{{ $applications->links('pagination::bootstrap-5') }}</div>
@endsection
