@extends('layouts.app')
@section('title', 'Detail Pendaftar')
@section('content')
<a href="{{ route('kampus.programs.show', $application->program) }}" class="small">&larr; {{ $application->program->title }}</a>
<div class="row g-4 mt-1">
    <div class="col-lg-7">
        <div class="card"><div class="card-body">
            <div class="d-flex justify-content-between">
                <h4>{{ $application->student->name }}</h4>
                @include('partials.application-badge', ['application' => $application])
            </div>
            <table class="table table-borderless">
                <tr><th width="35%">NIM</th><td>{{ $application->student->nim }}</td></tr>
                <tr><th>Program studi</th><td>{{ $application->student->prodi }}</td></tr>
                <tr><th>Email</th><td>{{ $application->student->email }}</td></tr>
                <tr><th>Tanggal daftar</th><td>{{ $application->created_at->translatedFormat('d F Y H:i') }}</td></tr>
                <tr><th>CV</th><td><a href="{{ route('kampus.applications.download', [$application, 'cv']) }}" target="_blank"><i class="bi bi-file-earmark-pdf"></i> Lihat CV</a></td></tr>
                <tr><th>Dokumen lain</th><td>
                    @if ($application->document_path)
                        <a href="{{ route('kampus.applications.download', [$application, 'dokumen']) }}" target="_blank"><i class="bi bi-paperclip"></i> Lihat dokumen</a>
                    @else - @endif
                </td></tr>
            </table>
            <h6>Motivasi</h6>
            <p class="pre-line">{{ $application->motivation ?: '-' }}</p>
        </div></div>
    </div>
    <div class="col-lg-5">
        <div class="card"><div class="card-body">
            <h5>Keputusan</h5>
            @include('partials.application-timeline')
            <form method="POST" action="{{ route('kampus.applications.status', $application) }}">@csrf @method('PATCH')
                <div class="mb-2">
                    @foreach (['diproses' => 'Diproses', 'disetujui' => 'Setujui', 'ditolak' => 'Tolak'] as $k => $v)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="s-{{ $k }}" value="{{ $k }}" @checked($application->status === $k)>
                            <label class="form-check-label" for="s-{{ $k }}">{{ $v }}</label>
                        </div>
                    @endforeach
                </div>
                <textarea name="admin_note" rows="3" class="form-control mb-2" placeholder="Catatan untuk mahasiswa (wajib jika ditolak)">{{ old('admin_note', $application->admin_note) }}</textarea>
                <button class="btn btn-primary w-100">Simpan & kirim notifikasi email</button>
            </form>
        </div></div>
    </div>
</div>
@endsection
