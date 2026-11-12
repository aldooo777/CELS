<?php

namespace App\Http\Controllers;

use App\Models\DemoRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Formulir "Minta Demo / Hubungi Kami" (guest) */
class DemoRequestController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'institution_name' => ['required', 'string', 'max:255'],
            'contact_name'     => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email', 'max:255'],
            'phone'            => ['required', 'string', 'max:30'],
            'student_estimate' => ['nullable', 'string', 'max:100'],
            'package'          => ['required', Rule::in(array_keys(DemoRequest::PACKAGES))],
            'message'          => ['nullable', 'string', 'max:3000'],
        ]);

        DemoRequest::create($data);

        return redirect(route('home').'#kontak')
            ->with('demo_success', 'Terima kasih. Permintaan demo/konsultasi Anda telah diterima. Tim CELS akan menghubungi dalam 1x24 jam kerja.');
    }
}
