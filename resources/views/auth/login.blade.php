@extends('layouts.auth')
@section('title', 'Masuk')
@section('full-footer', true)
@section('eyebrow', 'Masuk')
@section('heading', 'Masuk ke CELS')
@section('lead', 'Mahasiswa masuk dengan email kampus. Admin kampus masuk dengan email institusi yang didaftarkan.')

@section('form')
<form method="POST" action="{{ route('login') }}">@csrf
    <div class="mb-3">
        <label class="form-label" for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}"
               class="form-control @error('email') is-invalid @enderror" autocomplete="email" required autofocus>
    </div>
    <div class="mb-3">
        <label class="form-label" for="password">Kata sandi</label>
        <input type="password" id="password" name="password" class="form-control" autocomplete="current-password" required>
    </div>
    <div class="form-check mb-4">
        <input class="form-check-input" type="checkbox" name="remember" id="remember">
        <label class="form-check-label" for="remember">Ingat saya di perangkat ini</label>
    </div>
    <button type="submit" class="btn btn-cels-cta w-100 py-2">Masuk</button>
</form>
@endsection

@section('below')
    Belum punya akun? <a href="{{ route('register') }}">Daftar sebagai mahasiswa</a><br>
    Kampus Anda belum bergabung? <a href="{{ route('campus-registration.create') }}">Daftarkan kampus</a>
@endsection
