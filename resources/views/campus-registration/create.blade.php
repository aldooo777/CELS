@extends('layouts.app')
@section('title', 'Daftarkan Kampus')
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
@endpush
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card"><div class="card-body p-4">
            <h4><i class="bi bi-building-add"></i> Daftarkan Kampus</h4>
            <p class="text-muted">Setelah diverifikasi tim CELS, kampus Anda mendapat masa uji coba {{ \App\Models\Campus::TRIAL_DAYS }} hari untuk memposting program PDLK.</p>

            {{-- data-step-form: kolom diisi berurutan, kolom berikutnya terbuka setelah kolom sebelumnya benar --}}
            <form method="POST" action="{{ route('campus-registration.store') }}" enctype="multipart/form-data" data-step-form>@csrf

                <h6 class="text-uppercase text-muted small mt-3">1. Kampus</h6>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label" for="campus_type">Jenis kampus</label>
                        <select name="campus_type" id="campus_type" class="form-select" required>
                            <option value="">— Pilih jenis —</option>
                            <option value="PTN" @selected(old('campus_type') === 'PTN')>PTN (Negeri)</option>
                            <option value="PTS" @selected(old('campus_type') === 'PTS')>PTS (Swasta)</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label" for="campus_id">Nama kampus</label>
                        <select name="campus_id" id="campus_id" required></select>
                        <div class="form-text">Ketik nama atau kota kampus untuk mencari.</div>
                    </div>
                </div>
                <div id="new-campus" class="row g-3 mb-3" style="display:none">
                    <div class="col-md-8">
                        <label class="form-label" for="new_campus_name">Nama kampus</label>
                        <input id="new_campus_name" name="new_campus_name" value="{{ old('new_campus_name') }}" class="form-control"
                               maxlength="255" data-rule="starts-letter">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="new_campus_city">Kota</label>
                        <input id="new_campus_city" name="new_campus_city" value="{{ old('new_campus_city') }}" class="form-control"
                               maxlength="100" data-rule="starts-letter">
                    </div>
                </div>

                <h6 class="text-uppercase text-muted small mt-4">2. Data PIC (penanggung jawab)</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="pic_name">Nama PIC</label>
                        <input id="pic_name" name="pic_name" value="{{ old('pic_name') }}" class="form-control"
                               maxlength="255" data-rule="person-name" autocomplete="name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="pic_position">Jabatan</label>
                        <input id="pic_position" name="pic_position" value="{{ old('pic_position') }}" class="form-control"
                               placeholder="mis. Kepala Unit PDLK" maxlength="255" data-rule="starts-letter" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="pic_phone">No. telepon / WhatsApp</label>
                        <input type="tel" id="pic_phone" name="pic_phone" value="{{ old('pic_phone') }}"
                               class="form-control" inputmode="tel" maxlength="16" placeholder="081234567890"
                               data-rule="phone-id" autocomplete="tel" required>
                        <div class="form-text">Nomor HP aktif yang terhubung ke WhatsApp. Contoh: 081234567890</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="institution_email">Email institusi</label>
                        <input type="email" id="institution_email" name="institution_email" value="{{ old('institution_email') }}"
                               class="form-control" placeholder="pdlk@kampus.ac.id" data-rule="email-acid" autocomplete="email" required>
                        <div class="form-text">Dipakai untuk login. Harus berakhiran .ac.id.</div>
                    </div>
                </div>

                <h6 class="text-uppercase text-muted small mt-4">3. Dokumen pendukung</h6>
                <div class="mb-3">
                    <label class="form-label" for="document">Surat tugas / surat penunjukan PIC</label>
                    <input type="file" id="document" name="document" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                    <div class="form-text">PDF/JPG/PNG, maks. 5 MB.</div>
                </div>

                <h6 class="text-uppercase text-muted small mt-4">4. Kata sandi akun</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="password">Kata sandi</label>
                        <input type="password" id="password" name="password" class="form-control" minlength="8"
                               autocomplete="new-password" required>
                        <div class="form-text">Minimal 8 karakter.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="password_confirmation">Ulangi kata sandi</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                               data-match="password" autocomplete="new-password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-4">Kirim Pendaftaran</button>
            </form>
        </div></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
@php
    // Ubah data kampus menjadi daftar untuk JavaScript
    $campusOptions = $campuses->map(fn ($c) => [
        'value' => (string) $c->id,
        'text'  => $c->name.($c->city ? ' — '.$c->city : ''),
        'type'  => $c->type,
    ])->values();
@endphp
<script>
    const allCampuses = @json($campusOptions);
    const otherOption = { value: 'lainnya', text: 'Kampus tidak ada di daftar', type: 'semua' };
    const typeSelect  = document.getElementById('campus_type');
    const newCampus   = document.getElementById('new-campus');

    // Dropdown nama kampus yang bisa diketik untuk mencari
    const campusSelect = new TomSelect('#campus_id', {
        placeholder: 'Pilih jenis kampus dulu',
        maxOptions: 1000,
        onChange: toggleNewCampus,
    });

    // Isi dropdown hanya dengan kampus sesuai jenis (PTN / PTS)
    function loadCampuses(type, selected = '') {
        campusSelect.clear(true);
        campusSelect.clearOptions();

        if (!type) {
            campusSelect.settings.placeholder = 'Pilih jenis kampus dulu';
            campusSelect.inputState();
            campusSelect.disable();
            toggleNewCampus();
            return;
        }

        allCampuses.filter(c => c.type === type).forEach(c => campusSelect.addOption(c));
        campusSelect.addOption(otherOption);
        campusSelect.settings.placeholder = 'Ketik untuk mencari ' + type + '...';
        campusSelect.inputState();
        campusSelect.enable();
        if (selected) campusSelect.setValue(selected, true);
        toggleNewCampus();
    }

    // Tampilkan isian kampus baru jika memilih "Kampus tidak ada di daftar"
    // dan jadikan kolomnya wajib diisi hanya saat terlihat
    function toggleNewCampus() {
        const show = campusSelect.getValue() === 'lainnya';
        newCampus.style.display = show ? 'flex' : 'none';
        newCampus.querySelectorAll('input').forEach(input => input.required = show);
        if (window.CELS) window.CELS.refresh(typeSelect.form);
    }

    typeSelect.addEventListener('change', () => loadCampuses(typeSelect.value));
    loadCampuses(typeSelect.value, @json(old('campus_id', '')));

    // Nomor telepon: hanya angka, dan tanda + di depan
    document.getElementById('pic_phone').addEventListener('input', function () {
        this.value = this.value.replace(/(?!^\+)[^\d]/g, '');
    });
</script>
@endpush
