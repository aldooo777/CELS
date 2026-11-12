@extends('layouts.app')
@section('title', 'Masuk')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card"><div class="card-body p-4">
            <h4 class="mb-3">Masuk ke CELS</h4>
            <form method="POST" action="{{ route('login') }}">@csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kata sandi</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Ingat saya</label>
                </div>
                <button class="btn btn-primary w-100">Masuk</button>
            </form>
            <hr>
            <p class="small mb-0 text-center">Mahasiswa baru? <a href="{{ route('register') }}">Daftar di sini</a> · Admin kampus? <a href="{{ route('campus-registration.create') }}">Daftarkan kampus</a></p>
        </div></div>
    </div>
</div>
@endsection
