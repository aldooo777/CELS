@extends('layouts.app')
@section('title', 'Detail Pengajuan')
@section('content')
<a href="{{ route('admin.registrations.index') }}" class="small">&larr; Kembali</a>
<h3 class="mt-2">{{ $registration->campus->name }}</h3>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card"><div class="card-body">
            <table class="table table-borderless mb-0">
                <tr><th width="35%">Status</th><td>{{ $registration->statusLabel() }}</td></tr>
                <tr><th>Kampus</th><td>{{ $registration->campus->name }} ({{ $registration->campus->type }}{{ $registration->campus->city ? ', '.$registration->campus->city : '' }})
                    @unless($registration->campus->is_listed)<br><span class="badge text-bg-warning">Tidak ada di daftar PTN — periksa keabsahan kampus</span>@endunless</td></tr>
                <tr><th>Nama PIC</th><td>{{ $registration->pic_name }}</td></tr>
                <tr><th>Jabatan</th><td>{{ $registration->pic_position }}</td></tr>
                <tr><th>Telepon</th><td>{{ $registration->pic_phone }}</td></tr>
                <tr><th>Email institusi</th><td>{{ $registration->institution_email }}</td></tr>
                <tr><th>Dokumen</th><td><a href="{{ route('admin.registrations.document', $registration) }}" target="_blank"><i class="bi bi-file-earmark-pdf"></i> Lihat surat tugas</a></td></tr>
                <tr><th>Diajukan</th><td>{{ $registration->created_at->translatedFormat('d F Y H:i') }}</td></tr>
                @if ($registration->reviewed_at)
                    <tr><th>Diperiksa</th><td>{{ $registration->reviewed_at->translatedFormat('d F Y H:i') }} oleh {{ $registration->reviewer?->name ?? '-' }}</td></tr>
                    <tr><th>Catatan</th><td>{{ $registration->admin_note ?: '-' }}</td></tr>
                @endif
            </table>
        </div></div>
    </div>
    @if ($registration->status === 'menunggu')
    <div class="col-lg-5">
        <div class="card mb-3"><div class="card-body">
            <h5 class="text-success">Setujui</h5>
            <p class="small text-muted">Kampus masuk masa uji coba {{ \App\Models\Campus::TRIAL_DAYS }} hari, lalu ditawari langganan.</p>
            <form method="POST" action="{{ route('admin.registrations.approve', $registration) }}">@csrf
                <textarea name="admin_note" class="form-control mb-2" rows="2" placeholder="Catatan (opsional)"></textarea>
                <button class="btn btn-success w-100">Setujui</button>
            </form>
        </div></div>
        <div class="card"><div class="card-body">
            <h5 class="text-danger">Tolak</h5>
            <form method="POST" action="{{ route('admin.registrations.reject', $registration) }}">@csrf
                <textarea name="admin_note" class="form-control mb-2" rows="3" placeholder="Alasan penolakan (wajib)" required></textarea>
                <button class="btn btn-outline-danger w-100">Tolak</button>
            </form>
        </div></div>
    </div>
    @endif
</div>
@endsection
