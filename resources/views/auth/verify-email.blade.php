@extends('layouts.app')
@section('title', 'Verifikasi Email')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card text-center"><div class="card-body p-5">
            <i class="bi bi-envelope-check display-3 text-primary"></i>
            <h4 class="mt-3">Verifikasi email Anda</h4>
            <p class="text-muted">Kami telah mengirim link verifikasi ke <b>{{ auth()->user()->email }}</b>. Klik link tersebut untuk mulai melihat program.</p>
            <p class="small text-muted">Atau minta admin kampus Anda memverifikasi akun secara manual.</p>
            <form method="POST" action="{{ route('verification.send') }}">@csrf
                <button class="btn btn-outline-primary">Kirim ulang link verifikasi</button>
            </form>
        </div></div>
    </div>
</div>
@endsection
