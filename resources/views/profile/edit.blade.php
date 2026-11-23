@extends('layouts.app')
@section('title', 'Profil Saya')
@section('content')
<h3 class="mb-3">Profil Saya</h3>

<div class="row g-4">
    {{-- Kolom kiri: ringkasan akun + foto --}}
    <div class="col-lg-4">
        <div class="card profile-summary overflow-hidden">
            <div class="profile-summary-header text-center">
                @include('partials.avatar', ['user' => $user, 'size' => 112, 'ring' => true])
                <h5 class="text-white mt-3 mb-0">{{ $user->name }}</h5>
                <div class="small text-white-50 text-break">{{ $user->email }}</div>
                <div class="d-flex flex-wrap justify-content-center gap-1 mt-2">
                    <span class="badge rounded-pill account-badge-role">{{ $user->roleLabel() }}</span>
                    @if ($user->campus)
                        <span class="badge rounded-pill account-badge-campus">{{ $user->campus->short_name ?? $user->campus->name }}</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data">@csrf
                    <label class="form-label small fw-semibold" for="avatar">Ganti foto profil</label>
                    <input type="file" id="avatar" name="avatar" class="form-control form-control-sm @error('avatar') is-invalid @enderror"
                           accept=".jpg,.jpeg,.png,.webp" required>
                    @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">JPG, PNG, atau WEBP. Maksimal 2 MB.</div>
                    <button type="submit" class="btn btn-primary btn-sm w-100 mt-2"><i class="bi bi-upload me-1"></i>Unggah foto</button>
                </form>
                @if ($user->avatar_path)
                    <form method="POST" action="{{ route('profile.photo.destroy') }}" class="mt-2"
                          onsubmit="return confirm('Hapus foto profil?')">@csrf @method('DELETE')
                        <button type="submit" class="btn btn-link btn-sm text-danger w-100">Hapus foto</button>
                    </form>
                @endif

                <hr>
                <dl class="row small mb-0">
                    <dt class="col-5 text-muted fw-normal">Bergabung</dt>
                    <dd class="col-7">{{ $user->created_at?->translatedFormat('d F Y') }}</dd>
                    @if ($user->isMahasiswa())
                        <dt class="col-5 text-muted fw-normal">Status akun</dt>
                        <dd class="col-7">
                            @if ($user->hasVerifiedEmail())
                                <span class="badge text-bg-success">Terverifikasi</span>
                            @else
                                <span class="badge text-bg-warning">Menunggu verifikasi</span>
                            @endif
                        </dd>
                    @endif
                    @if ($user->isAdminKampus() && $user->campus)
                        <dt class="col-5 text-muted fw-normal">Status kampus</dt>
                        <dd class="col-7">{{ $user->campus->statusLabel() }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    {{-- Kolom kanan: ubah data + kata sandi --}}
    <div class="col-lg-8">
        <div class="card mb-4"><div class="card-body p-4">
            <h5 class="mb-3"><i class="bi bi-person-lines-fill me-1" style="color: var(--cels-navbar);"></i> Data akun</h5>
            <form method="POST" action="{{ route('profile.update') }}">@csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="name">Nama lengkap</label>
                        <input id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control"
                               maxlength="255" data-rule="person-name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="email">Email</label>
                        <input id="email" value="{{ $user->email }}" class="form-control" disabled>
                        <div class="form-text">Email dipakai untuk masuk dan tidak bisa diubah.</div>
                    </div>

                    @if ($user->isMahasiswa())
                        <div class="col-md-6">
                            <label class="form-label" for="nim">NIM</label>
                            <input id="nim" value="{{ $user->nim }}" class="form-control" disabled>
                            <div class="form-text">Hubungi admin kampus jika NIM salah.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="prodi">Program studi</label>
                            <input id="prodi" name="prodi" value="{{ old('prodi', $user->prodi) }}" class="form-control"
                                   maxlength="255" data-rule="starts-letter" required>
                        </div>
                    @endif

                    @if ($user->isAdminKampus())
                        <div class="col-md-6">
                            <label class="form-label" for="position">Jabatan</label>
                            <input id="position" name="position" value="{{ old('position', $user->position) }}" class="form-control"
                                   maxlength="255" data-rule="starts-letter" required>
                        </div>
                    @endif

                    @if ($user->campus)
                        <div class="col-md-6">
                            <label class="form-label" for="campus">Kampus</label>
                            <input id="campus" value="{{ $user->campus->name }}" class="form-control" disabled>
                        </div>
                    @endif

                    <div class="col-md-6">
                        <label class="form-label" for="phone">No. telepon / WhatsApp <span class="text-muted">(opsional)</span></label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control"
                               inputmode="tel" maxlength="16" placeholder="081234567890" data-rule="phone-id">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-4"><i class="bi bi-check2 me-1"></i>Simpan perubahan</button>
            </form>
        </div></div>

        <div class="card"><div class="card-body p-4">
            <h5 class="mb-3"><i class="bi bi-shield-lock me-1" style="color: var(--cels-navbar);"></i> Ganti kata sandi</h5>
            <form method="POST" action="{{ route('profile.password') }}">@csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" for="current_password">Kata sandi saat ini</label>
                        <input type="password" id="current_password" name="current_password" class="form-control"
                               autocomplete="current-password" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="password">Kata sandi baru</label>
                        <input type="password" id="password" name="password" class="form-control" minlength="8"
                               autocomplete="new-password" required>
                        <div class="form-text">Minimal 8 karakter.</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="password_confirmation">Ulangi kata sandi baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                               data-match="password" autocomplete="new-password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-outline-primary mt-4"><i class="bi bi-key me-1"></i>Ganti kata sandi</button>
            </form>
        </div></div>
    </div>
</div>
@endsection
