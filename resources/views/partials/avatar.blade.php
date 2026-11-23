{{-- Foto profil, atau inisial nama jika belum ada foto.
     Pakai: @include('partials.avatar', ['user' => $user, 'size' => 40, 'ring' => false]) --}}
@php
    $size = $size ?? 40;
    $initials = collect(preg_split('/\s+/', trim($user->name)))
        ->filter()->take(2)
        ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
        ->implode('');
@endphp
@if ($user->avatar_path)
    <img src="{{ route('profile.photo', $user) }}?v={{ $user->updated_at?->timestamp }}" alt="Foto {{ $user->name }}"
         class="avatar @if($ring ?? false) avatar-ring @endif" style="width:{{ $size }}px;height:{{ $size }}px">
@else
    <span class="avatar avatar-initials @if($ring ?? false) avatar-ring @endif"
          style="width:{{ $size }}px;height:{{ $size }}px;font-size:{{ round($size * 0.4) }}px" aria-hidden="true">{{ $initials }}</span>
@endif
