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

    /** Siapa yang boleh mendaftar */
    public const AUDIENCES = [
        'internal' => 'Hanya mahasiswa kampus ini',
        'semua'    => 'Terbuka untuk semua kampus',
    ];

    protected $fillable = [
        'campus_id', 'created_by', 'title', 'type', 'description', 'quota',
        'deadline', 'requirements', 'partner_info', 'is_open',
        'published_at', 'audience',
    ];

    protected function casts(): array
    {
        return [
            'deadline'     => 'date',
            'is_open'      => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function campus(): BelongsTo        { return $this->belongsTo(Campus::class); }
    public function creator(): BelongsTo       { return $this->belongsTo(User::class, 'created_by'); }
    public function applications(): HasMany    { return $this->hasMany(Application::class); }

    /** Program yang tampil ke mahasiswa: sudah terbit, dibuka, deadline belum lewat */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')
            ->where('is_open', true)
            ->whereDate('deadline', '>=', now()->toDateString());
    }

    /** Program yang sasaran pendaftarnya mencakup mahasiswa ini (kampus sendiri, atau kampus lain yang terbuka) */
    public function scopeOpenTo(Builder $query, User $user): Builder
    {
        return $query->where(fn ($q) => $q->where('campus_id', $user->campus_id)->orWhere('audience', 'semua'));
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null;
    }

    public function isOpenForAll(): bool
    {
        return $this->audience === 'semua';
    }

    public function audienceLabel(): string
    {
        return self::AUDIENCES[$this->audience] ?? $this->audience;
    }

    /** Sasaran pendaftar mencakup mahasiswa ini? */
    public function isOpenTo(?User $user): bool
    {
        return $user !== null
            && $user->isMahasiswa()
            && ($this->isOpenForAll() || $this->campus_id === $user->campus_id);
    }

    /** Mahasiswa ini boleh mendaftar sekarang? (terbit, sasaran cocok, masih menerima pendaftar) */
    public function canBeAppliedBy(?User $user): bool
    {
        return $this->isPublished() && $this->isOpenTo($user) && $this->isAcceptingApplications();
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
