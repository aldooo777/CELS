<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Judul program:
 * - diawali huruf atau angka (mis. "Magang ...", "2026 Program ...")
 * - memuat minimal 3 huruf
 * - hanya huruf, angka, spasi, dan tanda . , ' - / ( ) & :
 * Contoh yang lolos: "Magang Software Engineer", "Riset AI & Data (Semester Ganjil)"
 * Contoh yang ditolak: "-------", "!!! Magang", "12345"
 */
class ProgramTitle implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = trim((string) $value);

        if (! preg_match('/^[\pL\pN]/u', $value)) {
            $fail(':Attribute harus diawali huruf atau angka.');

            return;
        }

        if (! preg_match("/^[\pL\pN\s.,'\-\/()&:]+$/u", $value)) {
            $fail(':Attribute hanya boleh berisi huruf, angka, spasi, dan tanda . , \' - / ( ) & :');

            return;
        }

        if (preg_match_all('/\pL/u', $value) < 3) {
            $fail(':Attribute harus memuat minimal 3 huruf.');
        }
    }
}
