<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampusRegistration extends Model
{
    public const STATUS_LABELS = [
        'menunggu'  => 'Menunggu Verifikasi',
        'disetujui' => 'Disetujui',
        'ditolak'   => 'Ditolak',
    ];

    protected $fillable = [
        'campus_id', 'user_id', 'pic_name', 'pic_position', 'pic_phone',
        'institution_email', 'document_path', 'status', 'admin_note',
        'reviewed_by', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return ['reviewed_at' => 'datetime'];
    }

    public function campus(): BelongsTo   { return $this->belongsTo(Campus::class); }
    public function user(): BelongsTo     { return $this->belongsTo(User::class); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }
}
