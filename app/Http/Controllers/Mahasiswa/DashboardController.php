<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return view('mahasiswa.dashboard', [
            'user'         => $user->load('campus'),
            'applications' => $user->applications()->with('program')->latest()->take(5)->get(),
            'newPrograms'  => Program::available()->where('campus_id', $user->campus_id)->latest()->take(4)->get(),
        ]);
    }
}
