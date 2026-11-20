<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/** Halaman tunggu untuk mahasiswa yang belum diverifikasi admin kampus */
class VerificationController extends Controller
{
    public function notice(Request $request)
    {
        $user = $request->user();

        // Sudah diverifikasi → langsung ke dashboard
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        return view('auth.verify-email', [
            'user' => $user->load('campus'),
        ]);
    }
}
