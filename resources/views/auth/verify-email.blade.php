@extends('layouts.auth')
@section('title', 'Menunggu Verifikasi')
@section('eyebrow', 'Status akun')
@section('heading', 'Menunggu verifikasi kampus')
@section('lead')
    Akun Anda sudah terdaftar. Admin kampus <b>{{ $user->campus?->name ?? '-' }}</b> perlu memeriksa data Anda
    sebelum Anda bisa melihat dan mendaftar program PDLK.
@endsection
@section('width', '560px')

@section('form')
<div class="form-section">Data yang Anda kirim</div>
<dl class="row mb-4">
    <dt class="col-4 text-muted fw-normal">Nama</dt><dd class="col-8">{{ $user->name }}</dd>
    <dt class="col-4 text-muted fw-normal">NIM</dt><dd class="col-8">{{ $user->nim }}</dd>
    <dt class="col-4 text-muted fw-normal">Prodi</dt><dd class="col-8">{{ $user->prodi }}</dd>
    <dt class="col-4 text-muted fw-normal">Kampus</dt><dd class="col-8">{{ $user->campus?->name ?? '-' }}</dd>
    <dt class="col-4 text-muted fw-normal">Email</dt><dd class="col-8 text-break">{{ $user->email }}</dd>
</dl>
<p class="small text-muted">
    Halaman ini terbuka otomatis setelah akun diverifikasi. Jika sudah lebih dari dua hari kerja,
    hubungi unit kemahasiswaan atau PDLK kampus Anda.
</p>
<a href="{{ route('verification.notice') }}" class="btn btn-outline-primary w-100">Cek status</a>
@endsection
