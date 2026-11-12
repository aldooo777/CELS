<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campus extends Model
{
    /**
     * Status kampus di CELS:
     * - belum_terdaftar : ada di daftar PTN tapi belum mendaftar
     * - menunggu        : pengajuan sedang diverifikasi Admin CELS
     * - uji_coba        : disetujui, masa uji coba (trial)
     * - berlangganan    : sudah berlangganan
     * - nonaktif        : masa uji coba/langganan habis atau dinonaktifkan
     */
    public const STATUS_LABELS = [
        'belum_terdaftar' => 'Belum Terdaftar',
        'menunggu'        => 'Menunggu Verifikasi',
        'uji_coba'        => 'Masa Uji Coba',
        'berlangganan'    => 'Berlangganan',
        'nonaktif'        => 'Nonaktif',
    ];

    public const TRIAL_DAYS = 30;

    protected $fillable = [
        'name', 'short_name', 'type', 'city', 'province', 'email_domain',
        'is_listed', 'status', 'trial_ends_at', 'subscription_ends_at',
    ];

    protected function casts(): array
    {
        return [
            'is_listed'            => 'boolean',
            'trial_ends_at'        => 'date',
            'subscription_ends_at' => 'date',
        ];
    }

    public function users(): HasMany        { return $this->hasMany(User::class); }
    public function programs(): HasMany     { return $this->hasMany(Program::class); }
    public function registrations(): HasMany { return $this->hasMany(CampusRegistration::class); }

    public function students(): HasMany
    {
        return $this->hasMany(User::class)->where('role', User::ROLE_MAHASISWA);
    }

    /** Kampus yang sudah disetujui (boleh dipilih mahasiswa saat registrasi) */
    public function scopeJoined(Builder $query): Builder
    {
        return $query->whereIn('status', ['uji_coba', 'berlangganan']);
    }

    /** Kampus boleh memposting program jika uji coba / langganan masih berlaku */
    public function isActive(): bool
    {
        return match ($this->status) {
            'uji_coba'     => $this->trial_ends_at === null || $this->trial_ends_at->endOfDay()->isFuture(),
            'berlangganan' => $this->subscription_ends_at === null || $this->subscription_ends_at->endOfDay()->isFuture(),
            default        => false,
        };
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }
}
