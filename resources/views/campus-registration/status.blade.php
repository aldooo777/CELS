@extends('layouts.auth')
@section('title', 'Status Kampus')
@section('eyebrow', 'Status kampus')
@section('width', '560px')

@if ($registration?->status === 'menunggu')
    @section('heading', 'Pengajuan sedang diperiksa')
    @section('lead')
        Tim CELS sedang memeriksa data dan dokumen <b>{{ $campus->name }}</b>.
        Anda akan menerima email setelah pengajuan disetujui atau ditolak.
    @endsection
@elseif ($registration?->status === 'ditolak')
    @section('heading', 'Pengajuan ditolak')
    @section('lead', 'Pengajuan kampus Anda belum bisa kami setujui. Lihat alasannya di bawah.')
@else
    @section('heading', 'Akses kampus tidak aktif')
    @section('lead', 'Masa uji coba atau langganan kampus Anda telah berakhir.')
@endif

@section('form')
<div class="form-section">Ringkasan</div>
<dl class="row mb-4">
    <dt class="col-4 text-muted fw-normal">Kampus</dt><dd class="col-8">{{ $campus?->name ?? '-' }}</dd>
    @if ($registration)
        <dt class="col-4 text-muted fw-normal">Status</dt><dd class="col-8">{{ $registration->statusLabel() }}</dd>
        <dt class="col-4 text-muted fw-normal">Dikirim</dt><dd class="col-8">{{ $registration->created_at->translatedFormat('d F Y, H:i') }} WIB</dd>
        @if ($registration->status === 'ditolak')
            <dt class="col-4 text-muted fw-normal">Alasan</dt><dd class="col-8">{{ $registration->admin_note ?: '-' }}</dd>
        @endif
    @endif
</dl>
<p class="small text-muted mb-0">
    Pertanyaan seputar pengajuan atau langganan: <a href="mailto:{{ config('cels.contact.email') }}">{{ config('cels.contact.email') }}</a>
    atau WhatsApp {{ config('cels.contact.whatsapp') }}.
</p>
@endsection
