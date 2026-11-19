@extends('layouts.app')
@section('title', $student->name)
@section('content')
<a href="{{ route('admin.students.index') }}" class="small">&larr; Data Mahasiswa</a>
<div class="row g-4 mt-1">
    <div class="col-lg-4">
        <div class="card"><div class="card-body">
            <h4 class="mb-1">{{ $student->name }}</h4>
            @if ($student->email_verified_at)
                <span class="badge text-bg-success">Terverifikasi</span>
                @if ($student->verified_by_campus_at)<small class="text-muted">oleh admin kampus</small>@endif
            @else
                <span class="badge text-bg-secondary">Belum verifikasi</span>
            @endif
            <table class="table table-borderless small mt-3 mb-0">
                <tr><th>NIM</th><td>{{ $student->nim }}</td></tr>
                <tr><th>Prodi</th><td>{{ $student->prodi }}</td></tr>
                <tr><th>Kampus</th><td>{{ $student->campus?->name ?? '-' }}</td></tr>
                <tr><th>Email</th><td>{{ $student->email }}</td></tr>
                <tr><th>Terdaftar</th><td>{{ $student->created_at?->translatedFormat('d F Y') }}</td></tr>
            </table>
        </div></div>
    </div>
    <div class="col-lg-8">
        <div class="card"><div class="card-body">
            <h5>Riwayat Pendaftaran ({{ $applications->count() }})</h5>
            <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Program</th><th>Kampus</th><th>Tanggal</th><th>Status</th></tr></thead>
                <tbody>
                @forelse ($applications as $a)
                    <tr>
                        <td><a href="{{ route('admin.programs.show', $a->program) }}">{{ $a->program->title }}</a></td>
                        <td>{{ $a->program->campus->name }}</td>
                        <td>{{ $a->created_at->format('d/m/Y') }}</td>
                        <td>@include('partials.application-badge', ['application' => $a])</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-3">Belum pernah mendaftar program.</td></tr>
                @endforelse
                </tbody>
            </table>
            </div>
        </div></div>
    </div>
</div>
@endsection
