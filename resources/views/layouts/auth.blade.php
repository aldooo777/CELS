{{-- Kerangka halaman masuk/daftar: pita navy seperti hero beranda + kartu form di atasnya.
     Section yang dipakai: eyebrow, heading, lead, width, form, below --}}
@extends('layouts.app')
@section('body-class', 'page-auth')

@section('page')
<section class="auth-hero">
    <div class="container text-center">
        <span class="hero-tagline">@yield('eyebrow', config('cels.tagline'))</span>
        <h1 class="auth-title">@yield('heading')</h1>
        @hasSection('lead')
            <p class="auth-lead">@yield('lead')</p>
        @endif
    </div>
</section>

<div class="container auth-body">
    <div class="auth-card mx-auto" style="max-width: @yield('width', '460px')">
        @include('partials.flash')
        @yield('form')
    </div>
    @hasSection('below')
        <div class="auth-below mx-auto text-center" style="max-width: @yield('width', '460px')">@yield('below')</div>
    @endif
</div>
@endsection
