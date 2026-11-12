<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') · CELS - {{ config('cels.longname') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/cels.css') }}?v=2" rel="stylesheet">
    @stack('styles')
</head>
<body class="@yield('body-class')">

{{-- ============ NAVBAR DUA LAPIS (sticky) ============ --}}
<div class="sticky-nav-wrapper">
    {{-- Lapis atas: info + akun --}}
    <div class="nav-topbar">
        <div class="container d-flex justify-content-between align-items-center">
            <span class="d-none d-md-inline-flex align-items-center">
                <i class="bi bi-shield-check me-2 text-info"></i>
                {{ config('cels.longname') }} &bull; Layanan Resmi PDLK Perguruan Tinggi
            </span>
            <div class="d-flex align-items-center gap-3 ms-auto">
                @auth
                    <span class="d-none d-sm-inline">
                        <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
                        <span class="opacity-75">· {{ auth()->user()->roleLabel() }}@if(auth()->user()->campus) · {{ auth()->user()->campus->short_name ?? auth()->user()->campus->name }}@endif</span>
                    </span>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">@csrf
                        <button class="btn-login"><i class="bi bi-box-arrow-right me-1"></i> Keluar</button>
                    </form>
                @else
                    <a href="{{ route('register') }}"><i class="bi bi-mortarboard me-1"></i>Daftar Mahasiswa</a>
                    <a class="btn-login" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-1"></i> Masuk</a>
                @endauth
            </div>
        </div>
    </div>

    {{-- Lapis utama: logo + menu --}}
    <nav class="navbar navbar-expand-lg nav-main">
        <div class="container">
            <a class="navbar-brand text-decoration-none" href="{{ auth()->check() ? route('dashboard') : route('home') }}">
                <div class="d-flex align-items-center">
                    <span class="me-2 fw-bold text-white fs-3">CELS</span>
                    <span class="badge bg-white py-1 px-2 brand-badge">SaaS</span>
                </div>
                <span class="brand-tagline">{{ config('cels.tagline') }}</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#celsNavbarNav"
                    aria-controls="celsNavbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="celsNavbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                    @auth
                        @php($u = auth()->user())
                        @if ($u->isAdminCels())
                            <li class="nav-item"><a class="nav-link @if(request()->routeIs('admin.dashboard')) active @endif" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link @if(request()->routeIs('admin.registrations.*')) active @endif" href="{{ route('admin.registrations.index') }}">Verifikasi Kampus</a></li>
                            <li class="nav-item"><a class="nav-link @if(request()->routeIs('admin.campuses.*')) active @endif" href="{{ route('admin.campuses.index') }}">Kampus &amp; Langganan</a></li>
                            <li class="nav-item"><a class="nav-link @if(request()->routeIs('admin.demo-requests.*')) active @endif" href="{{ route('admin.demo-requests.index') }}">Permintaan Demo</a></li>
                        @elseif ($u->isAdminKampus() && $u->campus?->isActive())
                            <li class="nav-item"><a class="nav-link @if(request()->routeIs('kampus.dashboard')) active @endif" href="{{ route('kampus.dashboard') }}">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link @if(request()->routeIs('kampus.programs.*', 'kampus.applications.*')) active @endif" href="{{ route('kampus.programs.index') }}">Program</a></li>
                            <li class="nav-item"><a class="nav-link @if(request()->routeIs('kampus.students.*')) active @endif" href="{{ route('kampus.students.index') }}">Mahasiswa</a></li>
                            <li class="nav-item ms-lg-2 mt-2 mt-lg-0"><a class="btn btn-cels-cta text-nowrap" href="{{ route('kampus.programs.create') }}"><i class="bi bi-plus-lg"></i> Posting Program</a></li>
                        @elseif ($u->isMahasiswa() && $u->hasVerifiedEmail())
                            <li class="nav-item"><a class="nav-link @if(request()->routeIs('mahasiswa.dashboard')) active @endif" href="{{ route('mahasiswa.dashboard') }}">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link @if(request()->routeIs('mahasiswa.programs.*')) active @endif" href="{{ route('mahasiswa.programs.index') }}">Program</a></li>
                            <li class="nav-item"><a class="nav-link @if(request()->routeIs('mahasiswa.applications.*')) active @endif" href="{{ route('mahasiswa.applications.index') }}">Pendaftaran Saya</a></li>
                        @endif
                    @else
                        @php($home = request()->routeIs('home') ? '' : route('home'))
                        @foreach (['beranda' => 'Beranda', 'masalah' => 'Masalah', 'fitur' => 'Fitur', 'cara-kerja' => 'Cara Kerja', 'paket' => 'Paket', 'kabar' => 'Kabar', 'faq' => 'FAQ', 'kontak' => 'Kontak'] as $anchor => $label)
                            <li class="nav-item"><a class="nav-link" href="{{ $home }}#{{ $anchor }}">{{ $label }}</a></li>
                        @endforeach
                        <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                            <a class="btn btn-cels-cta text-nowrap" href="{{ route('campus-registration.create') }}">Daftarkan Kampus</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
</div>

<main>
    @hasSection('page')
        {{-- Halaman penuh (landing page) --}}
        @yield('page')
    @else
        <div class="container py-4">
            @include('partials.flash')
            @yield('content')
        </div>
    @endif
</main>

{{-- ============ FOOTER ============ --}}
@hasSection('full-footer')
    @include('partials.footer')
@else
    <footer class="cels-footer compact">
        <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center small text-white-50">
            <div>&copy; {{ date('Y') }} CELS - {{ config('cels.longname') }}</div>
            <div class="mt-2 mt-sm-0"><span class="text-warning">●</span> {{ config('cels.tagline') }}</div>
        </div>
    </footer>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
