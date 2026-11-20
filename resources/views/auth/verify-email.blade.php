@extends('layouts.app')
@section('title', 'Menunggu Verifikasi')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card text-center"><div class="card-body p-5">
            <i class="bi bi-hourglass-split display-3 text-warning"></i>
            <h4 class="mt-3">Akun menunggu verifikasi</h4>
            <p class="text-muted mb-4">
                Halo <b>{{ $user->name }}</b>, akun Anda sudah terdaftar. Admin kampus
                <b>{{ $user->campus?->name ?? '-' }}</b> akan memeriksa data Anda sebelum Anda bisa melihat dan mendaftar program PDLK.
            </p>

            <table class="table table-sm text-start mx-auto mb-4" style="max-width:420px">
                <tr><th class="text-muted fw-normal">Nama</th><td>{{ $user->name }}</td></tr>
                <tr><th class="text-muted fw-normal">NIM</th><td>{{ $user->nim }}</td></tr>
                <tr><th class="text-muted fw-normal">Prodi</th><td>{{ $user->prodi }}</td></tr>
                <tr><th class="text-muted fw-normal">Email</th><td>{{ $user->email }}</td></tr>
            </table>

            <p class="small text-muted mb-3">
                Sudah lama menunggu? Hubungi unit kemahasiswaan / PDLK kampus Anda.
                Halaman ini akan terbuka otomatis setelah akun diverifikasi.
            </p>
            <a href="{{ route('verification.notice') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-clockwise"></i> Cek status
            </a>
        </div></div>
    </div>
</div>
@endsection
