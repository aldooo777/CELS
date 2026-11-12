@extends('layouts.app')
@section('title', 'Daftarkan Kampus')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card"><div class="card-body p-4">
            <h4><i class="bi bi-building-add"></i> Daftarkan Kampus</h4>
            <p class="text-muted">Setelah diverifikasi tim CELS, kampus Anda mendapat masa uji coba {{ \App\Models\Campus::TRIAL_DAYS }} hari untuk memposting program PDLK.</p>

            <form method="POST" action="{{ route('campus-registration.store') }}" enctype="multipart/form-data">@csrf
                <h6 class="text-uppercase text-muted small mt-3">1. Kampus</h6>
                <div class="mb-3">
                    <select name="campus_id" id="campus_id" class="form-select" required>
                        <option value="">— Pilih kampus dari daftar PTN —</option>
                        @foreach ($campuses as $c)
                            <option value="{{ $c->id }}" @selected(old('campus_id') == $c->id)>{{ $c->name }}{{ $c->city ? ' — '.$c->city : '' }}</option>
                        @endforeach
                        <option value="lainnya" @selected(old('campus_id') === 'lainnya')>Kampus tidak ada di daftar</option>
                    </select>
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
                        <input name="pic_phone" value="{{ old('pic_phone') }}" class="form-control" required>
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
<script>
    const sel = document.getElementById('campus_id'), box = document.getElementById('new-campus');
    const toggle = () => box.style.display = sel.value === 'lainnya' ? 'flex' : 'none';
    sel.addEventListener('change', toggle); toggle();
</script>
@endpush
