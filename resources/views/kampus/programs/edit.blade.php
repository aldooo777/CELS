@extends('layouts.app')
@section('title', 'Ubah Program')
@section('content')
<div class="row justify-content-center"><div class="col-lg-9">
    <a href="{{ route('kampus.programs.show', $program) }}" class="small">&larr; Kembali</a>
    <div class="card mt-2"><div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Ubah Program</h4>
            <div>@include('partials.program-status')</div>
        </div>
        <form method="POST" action="{{ route('kampus.programs.update', $program) }}" class="mt-3">@csrf @method('PUT')
            @include('kampus.programs._form')
            <div class="d-flex flex-wrap gap-2 justify-content-end mt-4">
                @if ($program->isPublished())
                    @unless ($program->applications()->exists())
                        <button name="action" value="draft" class="btn btn-outline-secondary">Jadikan draf</button>
                    @endunless
                    <button class="btn btn-primary">Simpan perubahan</button>
                @else
                    <button class="btn btn-outline-secondary">Simpan draf</button>
                    <button name="action" value="publish" class="btn btn-primary">Posting sekarang</button>
                @endif
            </div>
        </form>
        <form method="POST" action="{{ route('kampus.programs.destroy', $program) }}" class="mt-3 text-end" onsubmit="return confirm('Hapus program ini? Jika sudah ada pendaftar, program hanya akan ditutup.')">@csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger">Hapus / tutup program</button>
        </form>
    </div></div>
</div></div>
@endsection
