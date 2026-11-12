<?php

namespace App\Http\Controllers\Kampus;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $campus = $request->user()->campus;

        $applications = Application::whereHas('program', fn ($q) => $q->where('campus_id', $campus->id));

        return view('kampus.dashboard', [
            'campus'         => $campus,
            'programCount'   => $campus->programs()->count(),
            'openCount'      => $campus->programs()->available()->count(),
            'newCount'       => (clone $applications)->where('status', Application::STATUS_DIAJUKAN)->count(),
            'approvedCount'  => (clone $applications)->where('status', Application::STATUS_DISETUJUI)->count(),
            'unverifiedCount'=> $campus->students()->whereNull('email_verified_at')->count(),
            'latest'         => (clone $applications)->with('program', 'student')->latest()->take(8)->get(),
        ]);
    }

    /** Halaman status: menunggu verifikasi / ditolak / masa uji coba habis */
    public function status(Request $request)
    {
        $user = $request->user();

        if ($user->campus?->isActive()) {
            return redirect()->route('kampus.dashboard');
        }

        return view('campus-registration.status', [
            'campus'       => $user->campus,
            'registration' => $user->campusRegistration,
        ]);
    }
}
