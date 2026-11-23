{{-- Menu akun di pojok kanan atas: klik untuk melihat detail akun --}}
@php($me = auth()->user()->loadMissing('campus'))
<div class="dropdown account-menu">
    <button class="account-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside"
            aria-expanded="false" aria-label="Menu akun">
        @include('partials.avatar', ['user' => $me, 'size' => 28])
        <span class="d-none d-sm-inline ms-2">{{ $me->name }}</span>
    </button>

    <div class="dropdown-menu dropdown-menu-end account-card shadow-lg">
        <div class="account-card-header">
            @include('partials.avatar', ['user' => $me, 'size' => 72, 'ring' => true])
            <div class="fw-semibold fs-6 mt-2 text-white">{{ $me->name }}</div>
            <div class="small text-white-50 text-break">{{ $me->email }}</div>
            <div class="d-flex flex-wrap justify-content-center gap-1 mt-2">
                <span class="badge rounded-pill account-badge-role">{{ $me->roleLabel() }}</span>
                @if ($me->campus)
                    <span class="badge rounded-pill account-badge-campus">{{ $me->campus->short_name ?? $me->campus->name }}</span>
                @endif
            </div>
        </div>

        <ul class="list-unstyled account-card-body mb-0">
            @if ($me->campus)
                <li><i class="bi bi-building"></i><span>{{ $me->campus->name }}</span></li>
            @endif
            @if ($me->isMahasiswa())
                <li><i class="bi bi-person-vcard"></i><span>NIM {{ $me->nim }}</span></li>
                <li><i class="bi bi-mortarboard"></i><span>{{ $me->prodi }}</span></li>
                <li>
                    <i class="bi bi-patch-check"></i>
                    @if ($me->hasVerifiedEmail())
                        <span class="text-success fw-semibold">Akun terverifikasi</span>
                    @else
                        <span class="text-warning fw-semibold">Menunggu verifikasi kampus</span>
                    @endif
                </li>
            @elseif ($me->isAdminKampus())
                <li><i class="bi bi-briefcase"></i><span>{{ $me->position ?: 'Jabatan belum diisi' }}</span></li>
                @if ($me->campus)
                    <li><i class="bi bi-stopwatch"></i><span>Status kampus: {{ $me->campus->statusLabel() }}</span></li>
                @endif
            @endif
            @if ($me->phone)
                <li><i class="bi bi-whatsapp"></i><span>{{ $me->phone }}</span></li>
            @endif
        </ul>

        <div class="account-card-footer">
            <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm flex-fill">
                <i class="bi bi-person-gear me-1"></i>Profil Saya
            </a>
            <form method="POST" action="{{ route('logout') }}" class="flex-fill">@csrf
                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                    <i class="bi bi-box-arrow-right me-1"></i>Keluar
                </button>
            </form>
        </div>
    </div>
</div>
