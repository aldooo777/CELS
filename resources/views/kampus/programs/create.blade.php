@extends('layouts.app')
@section('title', 'Posting Program')
@section('content')
<div class="row justify-content-center"><div class="col-lg-9">
    <a href="{{ route('kampus.programs.index') }}" class="small">&larr; Kembali</a>
    <div class="card mt-2"><div class="card-body p-4">
        <h4>Posting Program Baru</h4>
        <form method="POST" action="{{ route('kampus.programs.store') }}">@csrf
            @include('kampus.programs._form')
            <div class="d-flex flex-wrap gap-2 justify-content-end mt-4">
                <button name="action" value="draft" class="btn btn-outline-secondary">Simpan sebagai draf</button>
                <button name="action" value="publish" class="btn btn-primary">Posting sekarang</button>
            </div>
            <p class="small text-muted text-end mt-2 mb-0">Draf hanya terlihat oleh admin kampus dan bisa diposting kapan saja.</p>
        </form>
    </div></div>
</div></div>
@endsection
