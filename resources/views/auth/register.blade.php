@extends('layouts.app')
@section('title', 'Registrasi Mahasiswa')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card"><div class="card-body p-4">
            <h4>Registrasi Mahasiswa</h4>
            <p class="text-muted">Gratis. Gunakan email kampus (berakhiran <code>.ac.id</code>).</p>

            @if ($campuses->isEmpty())
                <div class="alert alert-warning">Belum ada kampus yang bergabung di CELS. Minta unit kemahasiswaan kampusmu untuk <a href="{{ route('campus-registration.create') }}">mendaftarkan kampus</a>.</div>
            @endif

            <form method="POST" action="{{ route('register') }}">@csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama lengkap</label>
                        <input name="name" value="{{ old('name') }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">NIM</label>
                        <input name="nim" value="{{ old('nim') }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kampus</label>
                        <select name="campus_id" class="form-select" required>
                            <option value="">— Pilih kampus —</option>
                            @foreach ($campuses as $c)
                                <option value="{{ $c->id }}" @selected(old('campus_id') == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Hanya kampus yang sudah bergabung di CELS.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Program studi</label>
                        <input name="prodi" value="{{ old('prodi') }}" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Email kampus</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="nama@students.kampus.ac.id" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kata sandi</label>
                        <input type="password" name="password" class="form-control" required minlength="8">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ulangi kata sandi</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                <button class="btn btn-primary w-100 mt-4" @disabled($campuses->isEmpty())>Daftar</button>
            </form>
        </div></div>
    </div>
</div>
@endsection
