<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Teks panjang (deskripsi, syarat) harus berisi kalimat sungguhan,
 * bukan hanya simbol/angka seperti "-----" atau "......".
 */
class MeaningfulText implements ValidationRule
{
    public function __construct(private int $minLetters = 10) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (preg_match_all('/\pL/u', (string) $value) < $this->minLetters) {
            $fail(':Attribute harus berisi kalimat yang jelas (minimal '.$this->minLetters.' huruf).');
        }
    }
}
