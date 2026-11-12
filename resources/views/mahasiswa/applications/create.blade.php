@extends('layouts.app')
@section('title', 'Daftar Program')
@section('content')
<div class="row justify-content-center"><div class="col-lg-8">
    <a href="{{ route('mahasiswa.programs.show', $program) }}" class="small">&larr; Kembali</a>
    <div class="card mt-2"><div class="card-body p-4">
        <h4>Daftar: {{ $program->title }}</h4>
        <h6 class="text-uppercase text-muted small mt-3">Data diri (otomatis dari profil)</h6>
        <div class="row g-2 mb-3">
            @foreach (['Nama' => $user->name, 'NIM' => $user->nim, 'Prodi' => $user->prodi, 'Kampus' => $user->campus->name, 'Email' => $user->email] as $label => $value)
                <div class="col-md-6"><label class="form-label small mb-0">{{ $label }}</label><input class="form-control" value="{{ $value }}" readonly disabled></div>
            @endforeach
        </div>
        <form method="POST" action="{{ route('mahasiswa.applications.store', $program) }}" enctype="multipart/form-data">@csrf
            <div class="mb-3">
                <label class="form-label">CV <span class="text-danger">*</span></label>
                <input type="file" name="cv" class="form-control" accept=".pdf" required>
                <div class="form-text">PDF, maks. 2 MB.</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Dokumen pendukung <span class="text-muted">(opsional)</span></label>
                <input type="file" name="document" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.zip">
                <div class="form-text">Transkrip, portofolio, sertifikat, dll. PDF/JPG/PNG/ZIP, maks. 5 MB.</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Motivasi <span class="text-muted">(opsional)</span></label>
                <textarea name="motivation" rows="4" class="form-control">{{ old('motivation') }}</textarea>
            </div>
            <button class="btn btn-primary w-100">Kirim Pendaftaran</button>
        </form>
    </div></div>
</div></div>
@endsection
