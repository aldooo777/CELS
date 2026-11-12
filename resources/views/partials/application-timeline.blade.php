@php
    $s = $application->status;
    $steps = [
        ['label' => 'Diajukan', 'done' => true, 'icon' => 'send'],
        ['label' => 'Diproses', 'done' => in_array($s, ['diproses','disetujui','ditolak']), 'icon' => 'hourglass-split'],
        ['label' => $s === 'ditolak' ? 'Ditolak' : 'Disetujui', 'done' => in_array($s, ['disetujui','ditolak']), 'icon' => $s === 'ditolak' ? 'x-lg' : 'check-lg', 'rejected' => $s === 'ditolak'],
    ];
@endphp
<div class="d-flex my-3">
    @foreach ($steps as $step)
        <div class="status-step {{ ($step['rejected'] ?? false) ? 'rejected' : ($step['done'] ? 'done' : '') }}">
            <div class="dot"><i class="bi bi-{{ $step['icon'] }}"></i></div>
            <div class="small mt-1">{{ $step['label'] }}</div>
        </div>
    @endforeach
</div>
