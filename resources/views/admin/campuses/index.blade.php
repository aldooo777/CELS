@extends('layouts.app')
@section('title', 'Kampus & Langganan')
@section('content')
<h3 class="mb-3">Kampus & Langganan</h3>
<form class="row g-2 mb-3">
    <div class="col-md-4">
        <select name="status" class="form-select" onchange="this.form.submit()">
            @foreach (['bergabung' => 'Sudah bergabung', 'uji_coba' => 'Masa uji coba', 'berlangganan' => 'Berlangganan', 'nonaktif' => 'Nonaktif', 'menunggu' => 'Menunggu verifikasi', 'belum_terdaftar' => 'Belum terdaftar', 'semua' => 'Semua'] as $k => $v)
                <option value="{{ $k }}" @selected($status === $k)>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6"><input name="q" value="{{ $search }}" class="form-control" placeholder="Cari nama kampus"></div>
    <div class="col-md-2"><button class="btn btn-outline-primary w-100">Cari</button></div>
</form>
<div class="card"><div class="table-responsive">
    <table class="table align-middle mb-0">
        <thead class="table-light"><tr><th>Kampus</th><th>Status</th><th>Program</th><th>Mahasiswa</th><th style="min-width:420px">Ubah status langganan</th></tr></thead>
        <tbody>
        @forelse ($campuses as $c)
            <tr>
                <td>{{ $c->name }}<br><small class="text-muted">{{ $c->city }}</small></td>
                <td>
                    <span class="badge text-bg-{{ $c->isActive() ? 'success' : 'secondary' }}">{{ $c->statusLabel() }}</span>
                    @if ($c->status === 'uji_coba' && $c->trial_ends_at)<br><small class="text-muted">s.d. {{ $c->trial_ends_at->format('d/m/Y') }}</small>@endif
                    @if ($c->status === 'berlangganan' && $c->subscription_ends_at)<br><small class="text-muted">s.d. {{ $c->subscription_ends_at->format('d/m/Y') }}</small>@endif
                </td>
                <td>{{ $c->programs_count }}</td>
                <td>{{ $c->students_count }}</td>
                <td>
                    @if (in_array($c->status, ['uji_coba', 'berlangganan', 'nonaktif']))
                    <form method="POST" action="{{ route('admin.campuses.subscription', $c) }}" class="row g-1">@csrf @method('PATCH')
                        <div class="col-4"><select name="status" class="form-select form-select-sm">
                            @foreach (['uji_coba' => 'Uji coba', 'berlangganan' => 'Berlangganan', 'nonaktif' => 'Nonaktif'] as $k => $v)
                                <option value="{{ $k }}" @selected($c->status === $k)>{{ $v }}</option>
                            @endforeach
                        </select></div>
                        <div class="col-3"><input type="date" name="trial_ends_at" value="{{ $c->trial_ends_at?->format('Y-m-d') }}" class="form-control form-control-sm" title="Akhir uji coba"></div>
                        <div class="col-3"><input type="date" name="subscription_ends_at" value="{{ $c->subscription_ends_at?->format('Y-m-d') }}" class="form-control form-control-sm" title="Akhir langganan"></div>
                        <div class="col-2"><button class="btn btn-sm btn-primary w-100">Simpan</button></div>
                    </form>
                    @else
                        <span class="text-muted small">—</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada kampus.</td></tr>
        @endforelse
        </tbody>
    </table>
</div></div>
<div class="mt-3">{{ $campuses->links('pagination::bootstrap-5') }}</div>
@endsection
