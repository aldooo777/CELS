@extends('layouts.app')
@section('title', 'Dashboard Mahasiswa')
@section('content')
<h3>Halo, {{ $user->name }}</h3>
<p class="text-muted">{{ $user->nim }} · {{ $user->prodi }} · {{ $user->campus->name }}</p>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card"><div class="card-body">
            <div class="d-flex justify-content-between"><h5>Program terbaru dari kampusmu</h5><a href="{{ route('mahasiswa.programs.index') }}" class="small">Lihat semua</a></div>
            @forelse ($newPrograms as $p)
                <a href="{{ route('mahasiswa.programs.show', $p) }}" class="d-block border rounded p-3 mb-2 text-reset text-decoration-none">
                    <span class="badge text-bg-primary">{{ $p->typeLabel() }}</span>
                    <div class="fw-semibold mt-1">{{ $p->title }}</div>
                    <small class="text-muted">Deadline {{ $p->deadline->translatedFormat('d F Y') }} · Kuota {{ $p->quota }}</small>
                </a>
            @empty
                <p class="text-muted mb-0">Belum ada program yang dibuka.</p>
            @endforelse
        </div></div>
    </div>
    <div class="col-lg-5">
        <div class="card"><div class="card-body">
            <div class="d-flex justify-content-between"><h5>Pendaftaran saya</h5><a href="{{ route('mahasiswa.applications.index') }}" class="small">Lihat semua</a></div>
            @forelse ($applications as $a)
                <div class="d-flex justify-content-between border-bottom py-2">
                    <a href="{{ route('mahasiswa.applications.show', $a) }}">{{ $a->program->title }}</a>
                    @include('partials.application-badge', ['application' => $a])
                </div>
            @empty
                <p class="text-muted mb-0">Kamu belum mendaftar program apa pun.</p>
            @endforelse
        </div></div>
    </div>
</div>
@endsection
