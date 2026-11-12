@extends('layouts.app')
@section('title', 'Posting Program')
@section('content')
<div class="row justify-content-center"><div class="col-lg-9">
    <a href="{{ route('kampus.programs.index') }}" class="small">&larr; Kembali</a>
    <div class="card mt-2"><div class="card-body p-4">
        <h4>Posting Program Baru</h4>
        <form method="POST" action="{{ route('kampus.programs.store') }}">@csrf
            @include('kampus.programs._form')
            <button class="btn btn-primary mt-4">Posting</button>
        </form>
    </div></div>
</div></div>
@endsection
