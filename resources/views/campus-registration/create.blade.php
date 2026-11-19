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

            <form method="POST" action="{{ route('campus-registration.store') }}" enctype="multipart/form-data">@csrf
                <h6 class="text-uppercase text-muted small mt-3">1. Kampus</h6>
                <div class="row g-3 mb-3">
    <div class="col-md-4">
        <label class="form-label">Jenis kampus</label>
        <select name="campus_type" id="campus_type" class="form-select" required>
            <option value="">— Pilih jenis —</option>
            <option value="PTN" @selected(old('campus_type') === 'PTN')>PTN (Negeri)</option>
            <option value="PTS" @selected(old('campus_type') === 'PTS')>PTS (Swasta)</option>
        </select>
    </div>
    <div class="col-md-8">
        <label class="form-label">Nama kampus</label>
        <select name="campus_id" id="campus_id" required></select>
        <div class="form-text">Ketik nama atau kota kampus untuk mencari.</div>
    </div>
</div>
                <div id="new-campus" class="row g-3 mb-3" style="display:none">
                    <div class="col-md-8">
                        <label class="form-label">Nama kampus</label>
                        <input name="new_campus_name" value="{{ old('new_campus_name') }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kota</label>
                        <input name="new_campus_city" value="{{ old('new_campus_city') }}" class="form-control">
                    </div>
                </div>

                <h6 class="text-uppercase text-muted small mt-4">2. Data PIC (penanggung jawab)</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama PIC</label>
                        <input name="pic_name" value="{{ old('pic_name') }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jabatan</label>
                        <input name="pic_position" value="{{ old('pic_position') }}" class="form-control" placeholder="mis. Kepala Unit PDLK" required>
                    </div>
                    <div class="col-md-6">
    <label class="form-label">No. telepon / WhatsApp</label>
    <input type="tel" name="pic_phone" id="pic_phone" value="{{ old('pic_phone') }}"
           class="form-control @error('pic_phone') is-invalid @enderror"
           inputmode="tel" maxlength="16" placeholder="081234567890"
           pattern="(\+62|62|0)8[0-9]{8,11}"
           title="Nomor HP Indonesia, contoh: 081234567890" required>
    <div class="form-text">Nomor HP aktif yang terhubung ke WhatsApp. Contoh: 081234567890</div>
    @error('pic_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
                    <div class="col-md-6">
                        <label class="form-label">Email institusi</label>
                        <input type="email" name="institution_email" value="{{ old('institution_email') }}" class="form-control" placeholder="pdlk@kampus.ac.id" required>
                        <div class="form-text">Dipakai untuk login. Harus berakhiran .ac.id.</div>
                    </div>
                </div>

                <h6 class="text-uppercase text-muted small mt-4">3. Dokumen pendukung</h6>
                <div class="mb-3">
                    <input type="file" name="document" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                    <div class="form-text">Surat tugas atau surat penunjukan PIC. PDF/JPG/PNG, maks. 5 MB.</div>
                </div>

                <h6 class="text-uppercase text-muted small mt-4">4. Kata sandi akun</h6>
                <div class="row g-3">
                    <div class="col-md-6"><input type="password" name="password" class="form-control" placeholder="Kata sandi (min. 8)" required></div>
                    <div class="col-md-6"><input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi kata sandi" required></div>
                </div>

                <button class="btn btn-primary w-100 mt-4">Kirim Pendaftaran</button>
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
    function toggleNewCampus() {
        newCampus.style.display = campusSelect.getValue() === 'lainnya' ? 'flex' : 'none';
    }

    typeSelect.addEventListener('change', () => loadCampuses(typeSelect.value));
    loadCampuses(typeSelect.value, @json(old('campus_id', '')));

    // Nomor telepon: hanya angka, dan tanda + di depan
    document.getElementById('pic_phone').addEventListener('input', function () {
        this.value = this.value.replace(/(?!^\+)[^\d]/g, '');
    });
</script>
@endpush
