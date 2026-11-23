@extends('layouts.app')
@section('title', 'Impor Mahasiswa')
@section('content')
<a href="{{ route('kampus.students.index') }}" class="small">&larr; Mahasiswa</a>
<h3 class="mt-2 mb-1">Impor Mahasiswa dari Excel</h3>
<p class="text-muted">Daftarkan banyak mahasiswa sekaligus. Akun yang didaftarkan kampus langsung terverifikasi.</p>

@if (! $import)
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card h-100"><div class="card-body p-4">
            <h5><span class="step-badge">1</span> Unduh template</h5>
            <p class="small text-muted">Isi satu mahasiswa per baris. Jangan ubah judul kolom di baris pertama.</p>
            <table class="table table-sm small mb-3">
                <thead><tr><th>Kolom</th><th>Keterangan</th></tr></thead>
                <tbody>
                    <tr><td><code>nama</code> <span class="text-danger">*</span></td><td>Diawali huruf, tanpa angka</td></tr>
                    <tr><td><code>nim</code> <span class="text-danger">*</span></td><td>Unik di kampus ini</td></tr>
                    <tr><td><code>prodi</code> <span class="text-danger">*</span></td><td>Program studi</td></tr>
                    <tr><td><code>email</code> <span class="text-danger">*</span></td><td>Email kampus berakhiran <code>.ac.id</code></td></tr>
                    <tr><td><code>kata_sandi</code></td><td>Opsional, minimal 8 karakter. Jika kosong, kata sandi awal = <b>NIM</b></td></tr>
                </tbody>
            </table>
            <a href="{{ route('kampus.students.import.template') }}" class="btn btn-outline-primary w-100">
                <i class="bi bi-file-earmark-excel me-1"></i> Unduh template Excel
            </a>
        </div></div>
    </div>
    <div class="col-lg-7">
        <div class="card h-100"><div class="card-body p-4">
            <h5><span class="step-badge">2</span> Unggah file</h5>
            <form method="POST" action="{{ route('kampus.students.import.preview') }}" enctype="multipart/form-data">@csrf
                <label class="form-label" for="file">File Excel / CSV</label>
                <input type="file" id="file" name="file" class="form-control @error('file') is-invalid @enderror"
                       accept=".xlsx,.csv" required>
                @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Format .xlsx atau .csv, maksimal 2 MB / 1.000 mahasiswa.</div>
                <button type="submit" class="btn btn-primary mt-3"><i class="bi bi-search me-1"></i> Periksa data</button>
            </form>
            <div class="alert alert-light border small mt-4 mb-0">
                <i class="bi bi-info-circle me-1"></i>
                Data <b>belum disimpan</b> saat diunggah. Anda akan melihat pratinjau dan hasil pengecekan setiap baris terlebih dahulu.
            </div>
        </div></div>
    </div>
</div>
@else
    @php($total = count($import['rows']))
    <div class="card mb-3"><div class="card-body d-flex flex-wrap align-items-center gap-3">
        <div class="me-auto">
            <div class="small text-muted">File</div>
            <div class="fw-semibold"><i class="bi bi-file-earmark-spreadsheet me-1"></i>{{ $import['file'] }}</div>
        </div>
        <span class="badge text-bg-light border fs-6">{{ $total }} baris</span>
        <span class="badge text-bg-success fs-6"><i class="bi bi-check-circle me-1"></i>{{ $validCount }} siap</span>
        @if ($total - $validCount)
            <span class="badge text-bg-danger fs-6"><i class="bi bi-x-circle me-1"></i>{{ $total - $validCount }} perlu diperbaiki</span>
        @endif
    </div></div>

    @if ($total - $validCount)
        <div class="alert alert-warning small">
            <i class="bi bi-exclamation-triangle me-1"></i>
            Baris berwarna merah akan <b>dilewati</b>. Perbaiki di file Excel lalu unggah ulang, atau lanjutkan untuk mendaftarkan baris yang sudah benar saja.
        </div>
    @endif

    <div class="card"><div class="table-responsive" style="max-height: 60vh">
        <table class="table table-sm align-middle mb-0 import-table">
            <thead class="table-light sticky-top"><tr>
                <th>Baris</th><th>Nama</th><th>NIM</th><th>Prodi</th><th>Email</th><th>Kata sandi awal</th><th>Hasil cek</th>
            </tr></thead>
            <tbody>
            @foreach ($import['rows'] as $row)
                <tr class="{{ $row['valid'] ? '' : 'table-danger' }}">
                    <td class="text-muted">{{ $row['line'] }}</td>
                    <td>{{ $row['data']['name'] }}</td>
                    <td>{{ $row['data']['nim'] }}</td>
                    <td>{{ $row['data']['prodi'] }}</td>
                    <td>{{ $row['data']['email'] }}</td>
                    <td class="small">{{ $row['data']['password'] !== '' ? 'Dari file' : 'NIM' }}</td>
                    <td class="small">
                        @if ($row['valid'])
                            <span class="text-success fw-semibold"><i class="bi bi-check-circle-fill"></i> Siap</span>
                        @else
                            <ul class="mb-0 ps-3 text-danger">@foreach ($row['errors'] as $e)<li>{{ $e }}</li>@endforeach</ul>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div></div>

    <div class="d-flex flex-wrap gap-2 mt-3">
        <form method="POST" action="{{ route('kampus.students.import.store') }}"
              onsubmit="return confirm('Daftarkan {{ $validCount }} mahasiswa sekarang?')">@csrf
            <button type="submit" class="btn btn-primary" @disabled($validCount === 0)>
                <i class="bi bi-person-plus me-1"></i> Daftarkan {{ $validCount }} mahasiswa
            </button>
        </form>
        <form method="POST" action="{{ route('kampus.students.import.cancel') }}">@csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-secondary">Batal / unggah file lain</button>
        </form>
    </div>
    <p class="small text-muted mt-3 mb-0">
        <i class="bi bi-shield-lock me-1"></i>
        Jika kata sandi dikosongkan, mahasiswa masuk dengan <b>email</b> dan <b>NIM</b> sebagai kata sandi awal.
        Minta mereka segera mengganti kata sandi di menu <b>Profil Saya</b>.
    </p>
@endif
@endsection
