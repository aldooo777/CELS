<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Campus;
use App\Models\CampusRegistration;
use App\Models\DemoRequest;
use App\Models\Program;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'pendingCount'   => CampusRegistration::where('status', 'menunggu')->count(),
            'trialCount'     => Campus::where('status', 'uji_coba')->count(),
            'subscribedCount'=> Campus::where('status', 'berlangganan')->count(),
            'studentCount'   => User::where('role', User::ROLE_MAHASISWA)->count(),
            'programCount'   => Program::count(),
            'applicationCount' => Application::count(),
            'demoCount'      => DemoRequest::where('is_handled', false)->count(),
            'latestPending'  => CampusRegistration::with('campus')->where('status', 'menunggu')->latest()->take(5)->get(),
        ]);
    }
}
