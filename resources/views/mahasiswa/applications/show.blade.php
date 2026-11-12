@extends('layouts.app')
@section('title', 'Status Pendaftaran')
@section('content')
<div class="row justify-content-center"><div class="col-lg-8">
    <a href="{{ route('mahasiswa.applications.index') }}" class="small">&larr; Pendaftaran saya</a>
    <div class="card mt-2"><div class="card-body p-4">
        <div class="d-flex justify-content-between">
            <div>
                <h4 class="mb-0">{{ $application->program->title }}</h4>
                <small class="text-muted">{{ $application->program->campus->name }} · {{ $application->program->typeLabel() }}</small>
            </div>
            @include('partials.application-badge')
        </div>

        @include('partials.application-timeline')

        <p class="small text-muted text-center">Terakhir diperbarui {{ ($application->status_changed_at ?? $application->updated_at)->translatedFormat('d F Y H:i') }}. Kamu akan menerima email setiap status berubah.</p>

        @if ($application->admin_note)
            <div class="alert alert-light border"><b>Catatan dari kampus:</b><br><span class="pre-line">{{ $application->admin_note }}</span></div>
        @endif

        @if ($application->status === 'disetujui')
            <div class="alert alert-success">
                <i class="bi bi-journal-text"></i> Selamat, kamu diterima! Fitur <b>logbook kegiatan</b> akan tersedia pada tahap berikutnya.
            </div>
        @endif
    </div></div>
</div></div>
@endsection
