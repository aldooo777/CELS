@extends('layouts.app')
@section('title', 'Verifikasi Kampus')
@section('content')
<h3 class="mb-3">Verifikasi Pendaftaran Kampus</h3>
<ul class="nav nav-pills mb-3">
    @foreach (['menunggu' => 'Menunggu', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak', 'semua' => 'Semua'] as $key => $label)
        <li class="nav-item"><a class="nav-link @if($status === $key) active @endif" href="{{ route('admin.registrations.index', ['status' => $key]) }}">{{ $label }}</a></li>
    @endforeach
</ul>
<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Kampus</th><th>PIC</th><th>Email institusi</th><th>Diajukan</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse ($registrations as $r)
            <tr>
                <td>{{ $r->campus->name }} @unless($r->campus->is_listed)<span class="badge text-bg-light border">di luar daftar</span>@endunless</td>
                <td>{{ $r->pic_name }}<br><small class="text-muted">{{ $r->pic_position }}</small></td>
                <td>{{ $r->institution_email }}</td>
                <td>{{ $r->created_at->format('d/m/Y') }}</td>
                <td><span class="badge text-bg-{{ ['menunggu'=>'warning','disetujui'=>'success','ditolak'=>'danger'][$r->status] }}">{{ $r->statusLabel() }}</span></td>
                <td><a href="{{ route('admin.registrations.show', $r) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data.</td></tr>
        @endforelse
        </tbody>
    </table>
</div></div>
<div class="mt-3">{{ $registrations->links('pagination::bootstrap-5') }}</div>
@endsection
