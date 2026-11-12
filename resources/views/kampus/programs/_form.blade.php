<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Judul program</label>
        <input name="title" value="{{ old('title', $program->title) }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Jenis</label>
        <select name="type" class="form-select" required>
            @foreach (\App\Models\Program::TYPES as $k => $v)
                <option value="{{ $k }}" @selected(old('type', $program->type) === $k)>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Deskripsi</label>
        <textarea name="description" rows="5" class="form-control" required>{{ old('description', $program->description) }}</textarea>
    </div>
    <div class="col-md-4">
        <label class="form-label">Kuota</label>
        <input type="number" min="1" name="quota" value="{{ old('quota', $program->quota) }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Deadline pendaftaran</label>
        <input type="date" name="deadline" value="{{ old('deadline', $program->deadline?->format('Y-m-d')) }}" class="form-control" required>
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check form-switch mb-2">
            <input type="hidden" name="is_open" value="0">
            <input class="form-check-input" type="checkbox" name="is_open" value="1" id="is_open" @checked(old('is_open', $program->is_open))>
            <label class="form-check-label" for="is_open">Pendaftaran dibuka</label>
        </div>
    </div>
    <div class="col-12">
        <label class="form-label">Syarat</label>
        <textarea name="requirements" rows="4" class="form-control" placeholder="Satu syarat per baris" required>{{ old('requirements', $program->requirements) }}</textarea>
    </div>
    <div class="col-12">
        <label class="form-label">Tautan / keterangan mitra <span class="text-muted">(opsional)</span></label>
        <textarea name="partner_info" rows="2" class="form-control" placeholder="mis. PT Contoh Jaya — www.contoh.co.id">{{ old('partner_info', $program->partner_info) }}</textarea>
    </div>
</div>
