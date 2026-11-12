@extends('layouts.app')
@section('title', 'Ubah Program')
@section('content')
<div class="row justify-content-center"><div class="col-lg-9">
    <a href="{{ route('kampus.programs.show', $program) }}" class="small">&larr; Kembali</a>
    <div class="card mt-2"><div class="card-body p-4">
        <h4>Ubah Program</h4>
        <form method="POST" action="{{ route('kampus.programs.update', $program) }}">@csrf @method('PUT')
            @include('kampus.programs._form')
            <div class="d-flex justify-content-between mt-4">
                <button class="btn btn-primary">Simpan</button>
            </div>
        </form>
        <form method="POST" action="{{ route('kampus.programs.destroy', $program) }}" class="mt-3 text-end" onsubmit="return confirm('Hapus program ini? Jika sudah ada pendaftar, program hanya akan ditutup.')">@csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Hapus / tutup program</button>
        </form>
    </div></div>
</div></div>
@endsection
