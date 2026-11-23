{{-- Penanda posting + status pendaftaran. Pakai: @include('partials.program-status', ['program' => $p]) --}}
@if (! $program->isPublished())
    <span class="badge text-bg-warning">Draf</span>
@else
    <span class="badge text-bg-primary">Terbit</span>
    @if (! $program->is_open)<span class="badge text-bg-secondary">Ditutup</span>
    @elseif ($program->deadline->endOfDay()->isPast())<span class="badge text-bg-secondary">Lewat deadline</span>
    @else<span class="badge text-bg-success">Dibuka</span>@endif
@endif
