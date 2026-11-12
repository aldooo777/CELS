<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    public const STATUS_DIAJUKAN  = 'diajukan';
    public const STATUS_DIPROSES  = 'diproses';
    public const STATUS_DISETUJUI = 'disetujui';
    public const STATUS_DITOLAK   = 'ditolak';

    public const STATUS_LABELS = [
        self::STATUS_DIAJUKAN  => 'Diajukan',
        self::STATUS_DIPROSES  => 'Diproses',
        self::STATUS_DISETUJUI => 'Disetujui',
        self::STATUS_DITOLAK   => 'Ditolak',
    ];

    public const STATUS_COLORS = [
        self::STATUS_DIAJUKAN  => 'secondary',
        self::STATUS_DIPROSES  => 'warning',
        self::STATUS_DISETUJUI => 'success',
        self::STATUS_DITOLAK   => 'danger',
    ];

    protected $fillable = [
        'program_id', 'user_id', 'motivation', 'cv_path', 'document_path',
        'status', 'admin_note', 'status_changed_at',
    ];

    protected function casts(): array
    {
        return ['status_changed_at' => 'datetime'];
    }

    public function program(): BelongsTo { return $this->belongsTo(Program::class); }
    public function student(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function statusColor(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'secondary';
    }

    public function isFinal(): bool
    {
        return in_array($this->status, [self::STATUS_DISETUJUI, self::STATUS_DITOLAK], true);
    }
}
