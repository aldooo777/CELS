<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

/** Registrasi mahasiswa (gratis) */
class StudentRegisterController extends Controller
{
    public function create()
    {
        return view('auth.register', [
            // Mahasiswa hanya bisa memilih kampus yang sudah bergabung di CELS
            'campuses' => Campus::joined()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'nim'       => ['required', 'string', 'max:30'],
            'prodi'     => ['required', 'string', 'max:255'],
            'campus_id' => ['required', 'exists:campuses,id'],
            'email'     => ['required', 'email', 'max:255', 'unique:users,email', 'regex:/\.ac\.id$/i'],
            'password'  => ['required', 'confirmed', Password::min(8)],
        ], [
            'email.regex' => 'Gunakan email kampus yang berakhiran .ac.id.',
        ]);

        $campus = Campus::joined()->findOrFail($data['campus_id']);

        $user = User::create([
            ...$data,
            'campus_id' => $campus->id,
            'role'      => User::ROLE_MAHASISWA,
        ]);

        // Mengirim email verifikasi (bawaan Laravel)
        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}
