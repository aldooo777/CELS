@extends('layouts.app')
@section('title', 'Program')
@section('content')
<h3>Program PDLK · {{ auth()->user()->campus->name }}</h3>
<form class="row g-2 my-3">
    <div class="col-md-3">
        <select name="type" class="form-select" onchange="this.form.submit()">
            <option value="">Semua jenis</option>
            @foreach ($types as $k => $v)<option value="{{ $k }}" @selected($type === $k)>{{ $v }}</option>@endforeach
        </select>
    </div>
    <div class="col-md-7"><input name="q" value="{{ $search }}" class="form-control" placeholder="Cari judul program"></div>
    <div class="col-md-2"><button class="btn btn-outline-primary w-100">Cari</button></div>
</form>
<div class="row g-3">
    @forelse ($programs as $p)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100"><div class="card-body d-flex flex-column">
                <div><span class="badge text-bg-primary">{{ $p->typeLabel() }}</span>
                    @if (in_array($p->id, $appliedIds))<span class="badge text-bg-success">Sudah daftar</span>@endif</div>
                <h5 class="mt-2">{{ $p->title }}</h5>
                <p class="small text-muted flex-grow-1">{{ \Illuminate\Support\Str::limit($p->description, 120) }}</p>
                <div class="small mb-2"><i class="bi bi-calendar-event"></i> Deadline {{ $p->deadline->translatedFormat('d M Y') }} · <i class="bi bi-people"></i> Kuota {{ $p->quota }}</div>
                <a href="{{ route('mahasiswa.programs.show', $p) }}" class="btn btn-outline-primary btn-sm">Lihat detail</a>
            </div></div>
        </div>
    @empty
        <div class="col-12"><div class="alert alert-light border">Belum ada program yang dibuka oleh kampusmu.</div></div>
    @endforelse
</div>
<div class="mt-3">{{ $programs->links('pagination::bootstrap-5') }}</div>
@endsection
