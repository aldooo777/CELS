<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Nama orang: wajib diawali huruf, dan hanya boleh berisi
 * huruf, spasi, titik, koma, tanda petik, dan tanda hubung.
 * Contoh yang lolos: "Budi Santoso", "Dr. Siti Aisyah, M.Kom.", "O'Neil"
 */
class PersonName implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = trim((string) $value);

        if (! preg_match('/^\pL/u', $value)) {
            $fail(':Attribute harus diawali huruf.');

            return;
        }

        if (! preg_match("/^[\pL\s.,'\-]+$/u", $value)) {
            $fail(':Attribute hanya boleh berisi huruf, spasi, titik, koma, tanda petik, dan tanda hubung.');
        }
    }
}
