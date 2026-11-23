@extends('layouts.auth')
@section('title', 'Registrasi Mahasiswa')
@section('full-footer', true)
@section('eyebrow', 'Pendaftaran mahasiswa')
@section('heading', 'Buat akun mahasiswa')
@section('lead', 'Gratis. Gunakan email kampus berakhiran .ac.id. Akun aktif setelah diverifikasi admin kampus Anda.')
@section('width', '760px')

@section('form')
@if ($campuses->isEmpty())
    <div class="alert alert-warning">
        Belum ada kampus yang bergabung di CELS. Minta unit kemahasiswaan kampusmu untuk
        <a href="{{ route('campus-registration.create') }}">mendaftarkan kampus</a>.
    </div>
@endif

{{-- data-step-form: kolom diisi berurutan, kolom berikutnya terbuka setelah kolom sebelumnya benar --}}
<form method="POST" action="{{ route('register') }}" data-step-form>@csrf
    <div class="form-section">Data diri</div>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="name">Nama lengkap</label>
            <input id="name" name="name" value="{{ old('name') }}" class="form-control" maxlength="255"
                   data-rule="person-name" autocomplete="name" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="nim">NIM</label>
            <input id="nim" name="nim" value="{{ old('nim') }}" class="form-control" maxlength="30" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="campus_id">Kampus</label>
            <select id="campus_id" name="campus_id" class="form-select" required>
                <option value="">— Pilih kampus —</option>
                @foreach ($campuses as $c)
                    <option value="{{ $c->id }}" @selected(old('campus_id') == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
            <div class="form-text">Hanya kampus yang sudah bergabung di CELS.</div>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="prodi">Program studi</label>
            <input id="prodi" name="prodi" value="{{ old('prodi') }}" class="form-control" maxlength="255"
                   data-rule="starts-letter" required>
        </div>
    </div>

    <div class="form-section">Akun</div>
    <div class="row g-3">
        <div class="col-12">
            <label class="form-label" for="email">Email kampus</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control"
                   placeholder="nama@students.kampus.ac.id" data-rule="email-acid" autocomplete="email" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="password">Kata sandi</label>
            <input type="password" id="password" name="password" class="form-control" minlength="8"
                   autocomplete="new-password" required>
            <div class="form-text">Minimal 8 karakter.</div>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="password_confirmation">Ulangi kata sandi</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                   data-match="password" autocomplete="new-password" required>
        </div>
    </div>
    <button type="submit" class="btn btn-cels-cta w-100 py-2 mt-4" @disabled($campuses->isEmpty())>Daftar</button>
</form>
@endsection

@section('below')
    Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
@endsection
