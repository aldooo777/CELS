<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemoRequest extends Model
{
    public const PACKAGES = [
        'belum_tahu' => 'Belum tahu (butuh konsultasi)',
        'dasar'      => 'Paket Dasar',
        'premium'    => 'Paket Premium',
    ];

    protected $fillable = [
        'institution_name', 'contact_name', 'email', 'phone',
        'student_estimate', 'package', 'message', 'is_handled',
    ];

    protected function casts(): array
    {
        return ['is_handled' => 'boolean'];
    }

    public function packageLabel(): string
    {
        return self::PACKAGES[$this->package] ?? $this->package;
    }
}
