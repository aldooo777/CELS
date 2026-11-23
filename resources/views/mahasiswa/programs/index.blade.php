@extends('layouts.app')
@section('title', 'Program')
@section('content')
@php($me = auth()->user())
<h3>Program PDLK</h3>
<p class="text-muted">Program dari kampusmu dan kampus lain yang bergabung di CELS.</p>
<form class="row g-2 my-3">
    <div class="col-md-3">
        <select name="kampus" class="form-select" onchange="this.form.submit()">
            <option value="semua" @selected($scope === 'semua')>Semua kampus</option>
            <option value="saya" @selected($scope === 'saya')>Kampus saya ({{ $me->campus->short_name ?? $me->campus->name }})</option>
            <option value="bisa" @selected($scope === 'bisa')>Yang bisa saya daftar</option>
            <optgroup label="Kampus tertentu">
                @foreach ($campuses as $c)
                    <option value="{{ $c->id }}" @selected($scope === (string) $c->id)>{{ $c->name }}</option>
                @endforeach
            </optgroup>
        </select>
    </div>
    <div class="col-md-3">
        <select name="type" class="form-select" onchange="this.form.submit()">
            <option value="">Semua jenis</option>
            @foreach ($types as $k => $v)<option value="{{ $k }}" @selected($type === $k)>{{ $v }}</option>@endforeach
        </select>
    </div>
    <div class="col-md-4"><input name="q" value="{{ $search }}" class="form-control" placeholder="Cari judul program"></div>
    <div class="col-md-2"><button class="btn btn-outline-primary w-100">Cari</button></div>
</form>
<div class="row g-3">
    @forelse ($programs as $p)
        @php($own = $p->campus_id === $me->campus_id)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100"><div class="card-body d-flex flex-column">
                <div class="d-flex flex-wrap gap-1">
                    <span class="badge text-bg-primary">{{ $p->typeLabel() }}</span>
                    @if (in_array($p->id, $appliedIds))
                        <span class="badge text-bg-success">Sudah daftar</span>
                    @elseif (! $own && ! $p->isOpenForAll())
                        <span class="badge text-bg-secondary">Khusus mahasiswa {{ $p->campus->short_name ?? $p->campus->name }}</span>
                    @elseif (! $own)
                        <span class="badge text-bg-info">Terbuka untuk semua kampus</span>
                    @endif
                </div>
                <h5 class="mt-2 mb-1">{{ $p->title }}</h5>
                <div class="small text-muted mb-2">{{ $p->campus->name }}@if ($own) · kampus saya @endif</div>
                <p class="small text-muted flex-grow-1">{{ \Illuminate\Support\Str::limit($p->description, 120) }}</p>
                <div class="small mb-2">Deadline {{ $p->deadline->translatedFormat('d M Y') }} · Kuota {{ $p->quota }}</div>
                <a href="{{ route('mahasiswa.programs.show', $p) }}" class="btn btn-outline-primary btn-sm">Lihat detail</a>
            </div></div>
        </div>
    @empty
        <div class="col-12"><div class="alert alert-light border">Belum ada program yang sesuai dengan filter ini.</div></div>
    @endforelse
</div>
<div class="mt-3">{{ $programs->links('pagination::bootstrap-5') }}</div>
@endsection
