@extends('layouts.app')
@section('title', $program->title)
@section('content')
<a href="{{ route('mahasiswa.programs.index') }}" class="small">&larr; Semua program</a>
<div class="row g-4 mt-1">
    <div class="col-lg-8">
        <div class="card"><div class="card-body p-4">
            <span class="badge text-bg-primary">{{ $program->typeLabel() }}</span>
            <h3 class="mt-2">{{ $program->title }}</h3>
            <p class="text-muted">{{ $program->campus->name }}</p>
            <h6>Deskripsi</h6><p class="pre-line">{{ $program->description }}</p>
            <h6>Syarat</h6><p class="pre-line">{{ $program->requirements }}</p>
            @if ($program->partner_info)<h6>Mitra</h6><p class="pre-line">{{ $program->partner_info }}</p>@endif
        </div></div>
    </div>
    <div class="col-lg-4">
        <div class="card"><div class="card-body">
            <p class="mb-1"><i class="bi bi-calendar-event"></i> Deadline: <b>{{ $program->deadline->translatedFormat('d F Y') }}</b></p>
            <p><i class="bi bi-people"></i> Kuota: <b>{{ $program->quota }}</b> orang</p>
            @if ($application)
                <div class="alert alert-info mb-2">Kamu sudah mendaftar. Status: @include('partials.application-badge')</div>
                <a href="{{ route('mahasiswa.applications.show', $application) }}" class="btn btn-outline-primary w-100">Lihat pendaftaran</a>
            @elseif ($program->isAcceptingApplications())
                <a href="{{ route('mahasiswa.applications.create', $program) }}" class="btn btn-primary w-100 btn-lg">Daftar</a>
            @else
                <button class="btn btn-secondary w-100" disabled>Pendaftaran ditutup</button>
            @endif
        </div></div>
    </div>
</div>
@endsection
