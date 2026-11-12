<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN_CELS   = 'admin_cels';
    public const ROLE_ADMIN_KAMPUS = 'admin_kampus';
    public const ROLE_MAHASISWA    = 'mahasiswa';

    public const ROLE_LABELS = [
        self::ROLE_ADMIN_CELS   => 'Admin CELS',
        self::ROLE_ADMIN_KAMPUS => 'Admin Kampus',
        self::ROLE_MAHASISWA    => 'Mahasiswa',
    ];

    protected $fillable = [
        'name', 'email', 'password', 'role', 'campus_id',
        'nim', 'prodi', 'phone', 'position', 'email_verified_at', 'verified_by_campus_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at'     => 'datetime',
            'verified_by_campus_at' => 'datetime',
            'password'              => 'hashed',
        ];
    }

    /* ---------- Relasi ---------- */

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function campusRegistration(): HasOne
    {
        return $this->hasOne(CampusRegistration::class)->latestOfMany();
    }

    /* ---------- Helper peran ---------- */

    public function isAdminCels(): bool   { return $this->role === self::ROLE_ADMIN_CELS; }
    public function isAdminKampus(): bool { return $this->role === self::ROLE_ADMIN_KAMPUS; }
    public function isMahasiswa(): bool   { return $this->role === self::ROLE_MAHASISWA; }

    public function roleLabel(): string
    {
        return self::ROLE_LABELS[$this->role] ?? $this->role;
    }

    /** Route dashboard sesuai peran */
    public function homeRoute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN_CELS   => 'admin.dashboard',
            self::ROLE_ADMIN_KAMPUS => 'kampus.dashboard',
            default                 => 'mahasiswa.dashboard',
        };
    }
}
