<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Teks umum (jabatan, prodi, nama kampus, kota, nama institusi):
 * - wajib diawali huruf
 * - hanya boleh berisi huruf, angka, spasi, dan tanda . , ' - / ( )
 * Contoh yang lolos: "Kepala Unit PDLK", "Universitas 17 Agustus 1945", "Staf Kemahasiswaan (Fakultas Teknik)"
 * Contoh yang ditolak: "1 Kepala", "jabatan saya &&&", "Staf #1"
 */
class StartsWithLetter implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = trim((string) $value);

        if (! preg_match('/^\pL/u', $value)) {
            $fail(':Attribute harus diawali huruf.');

            return;
        }

        if (! preg_match("/^[\pL\pN\s.,'\-\/()]+$/u", $value)) {
            $fail(':Attribute hanya boleh berisi huruf, angka, spasi, dan tanda . , \' - / ( )');
        }
    }
}
