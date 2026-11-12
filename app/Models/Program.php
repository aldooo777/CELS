<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    public const TYPES = [
        'magang'             => 'Magang',
        'kerja_praktik'      => 'Kerja Praktik',
        'riset'              => 'Riset',
        'pertukaran_pelajar' => 'Pertukaran Pelajar',
        'kkn'                => 'KKN',
        'studi_independen'   => 'Studi Independen',
        'lainnya'            => 'Lainnya',
    ];

    protected $fillable = [
        'campus_id', 'created_by', 'title', 'type', 'description', 'quota',
        'deadline', 'requirements', 'partner_info', 'is_open',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'is_open'  => 'boolean',
        ];
    }

    public function campus(): BelongsTo        { return $this->belongsTo(Campus::class); }
    public function creator(): BelongsTo       { return $this->belongsTo(User::class, 'created_by'); }
    public function applications(): HasMany    { return $this->hasMany(Application::class); }

    /** Program yang masih bisa didaftar: dibuka & deadline belum lewat */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_open', true)->whereDate('deadline', '>=', now()->toDateString());
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function approvedCount(): int
    {
        return $this->applications()->where('status', Application::STATUS_DISETUJUI)->count();
    }

    public function isQuotaFull(): bool
    {
        return $this->approvedCount() >= $this->quota;
    }

    public function isAcceptingApplications(): bool
    {
        return $this->is_open && ! $this->deadline->endOfDay()->isPast() && ! $this->isQuotaFull();
    }
}
