@extends('layouts.app')
@section('title', 'Status Kampus')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card text-center"><div class="card-body p-5">
            @if ($registration?->status === 'menunggu')
                <i class="bi bi-hourglass-split display-3 text-warning"></i>
                <h4 class="mt-3">Menunggu verifikasi</h4>
                <p class="text-muted">Pendaftaran <b>{{ $campus->name }}</b> sedang diperiksa tim CELS. Anda akan menerima email setelah pengajuan disetujui atau ditolak.</p>
                <p class="small text-muted mb-0">Dikirim {{ $registration->created_at->translatedFormat('d F Y H:i') }}</p>
            @elseif ($registration?->status === 'ditolak')
                <i class="bi bi-x-circle display-3 text-danger"></i>
                <h4 class="mt-3">Pendaftaran ditolak</h4>
                <p class="text-muted">Alasan: {{ $registration->admin_note ?: '-' }}</p>
                <p class="small">Silakan hubungi tim CELS atau daftarkan ulang dengan email institusi lain.</p>
            @else
                <i class="bi bi-lock display-3 text-secondary"></i>
                <h4 class="mt-3">Akses kampus tidak aktif</h4>
                <p class="text-muted">Masa uji coba atau langganan {{ $campus?->name }} telah berakhir. Hubungi tim CELS untuk berlangganan.</p>
            @endif
        </div></div>
    </div>
</div>
@endsection
