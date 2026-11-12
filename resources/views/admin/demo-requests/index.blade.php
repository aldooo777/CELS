@extends('layouts.app')
@section('title', 'Permintaan Demo')
@section('content')
<h3 class="mb-1">Permintaan Demo</h3>
<p class="text-muted">Masuk dari formulir "Minta Demo / Hubungi Kami" di landing page. Calon pelanggan: tindak lanjuti lalu tandai selesai.</p>
<div class="card"><div class="table-responsive">
    <table class="table align-middle mb-0">
        <thead class="table-light"><tr><th>Institusi</th><th>Kontak</th><th>Paket</th><th>Pesan</th><th>Masuk</th><th>Status</th></tr></thead>
        <tbody>
        @forelse ($requests as $r)
            <tr @class(['opacity-50' => $r->is_handled])>
                <td class="fw-semibold">{{ $r->institution_name }}<br><small class="text-muted fw-normal">{{ $r->student_estimate ?: '-' }}</small></td>
                <td>{{ $r->contact_name }}<br>
                    <small><a href="mailto:{{ $r->email }}">{{ $r->email }}</a> · {{ $r->phone }}</small></td>
                <td><span class="badge text-bg-light border">{{ $r->packageLabel() }}</span></td>
                <td class="small" style="max-width:280px">{{ \Illuminate\Support\Str::limit($r->message, 120) ?: '-' }}</td>
                <td class="small">{{ $r->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.demo-requests.toggle', $r) }}">@csrf @method('PATCH')
                        <button class="btn btn-sm {{ $r->is_handled ? 'btn-outline-secondary' : 'btn-primary' }}">
                            {{ $r->is_handled ? 'Selesai ✓' : 'Tandai selesai' }}
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada permintaan demo.</td></tr>
        @endforelse
        </tbody>
    </table>
</div></div>
<div class="mt-3">{{ $requests->links('pagination::bootstrap-5') }}</div>
@endsection
