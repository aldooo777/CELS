<?php

namespace App\Http\Controllers;

use App\Models\DemoRequest;
use App\Rules\PersonName;
use App\Rules\StartsWithLetter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Formulir "Minta Demo / Hubungi Kami" (guest) */
class DemoRequestController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'institution_name' => ['required', 'string', 'max:255', new StartsWithLetter],
            'contact_name'     => ['required', 'string', 'max:255', new PersonName],
            'email'            => ['required', 'email', 'max:255'],
            'phone'            => ['required', 'regex:/^(\+62|62|0)8[0-9]{8,11}$/'],
            'student_estimate' => ['nullable', 'string', 'max:100'],
            'package'          => ['required', Rule::in(array_keys(DemoRequest::PACKAGES))],
            'message'          => ['nullable', 'string', 'max:3000'],
        ], [
            'phone.regex' => 'Nomor telepon harus nomor HP Indonesia, contoh: 081234567890.',
        ]);

        // Samakan format nomor: +628... / 628... menjadi 08...
        $data['phone'] = preg_replace('/^(\+62|62)/', '0', $data['phone']);

        DemoRequest::create($data);

        return redirect(route('home').'#kontak')
            ->with('demo_success', 'Terima kasih. Permintaan demo/konsultasi Anda telah diterima. Tim CELS akan menghubungi dalam 1x24 jam kerja.');
    }
}
