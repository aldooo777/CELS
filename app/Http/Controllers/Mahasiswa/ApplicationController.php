<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Program;
use App\Notifications\ApplicationStatusChanged;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        return view('mahasiswa.applications.index', [
            'applications' => $request->user()->applications()->with('program.campus')->latest()->paginate(10),
        ]);
    }

    public function create(Request $request, Program $program)
    {
        if ($redirect = $this->guard($request, $program)) {
            return $redirect;
        }

        return view('mahasiswa.applications.create', [
            'program' => $program,
            'user'    => $request->user()->load('campus'),   // profil terisi otomatis
        ]);
    }

    public function store(Request $request, Program $program)
    {
        if ($redirect = $this->guard($request, $program)) {
            return $redirect;
        }

        $data = $request->validate([
            'motivation' => ['nullable', 'string', 'max:3000'],
            'cv'         => ['required', 'file', 'mimes:pdf', 'max:2048'],
            'document'   => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,zip', 'max:5120'],
        ]);

        $application = $request->user()->applications()->create([
            'program_id'        => $program->id,
            'motivation'        => $data['motivation'] ?? null,
            'cv_path'           => $request->file('cv')->store('applications/cv'),
            'document_path'     => $request->file('document')?->store('applications/documents'),
            'status'            => Application::STATUS_DIAJUKAN,
            'status_changed_at' => now(),
        ]);

        $request->user()->notify(new ApplicationStatusChanged($application));

        return redirect()->route('mahasiswa.applications.show', $application)
            ->with('success', 'Pendaftaran berhasil dikirim.');
    }

    public function show(Request $request, Application $application)
    {
        abort_unless($application->user_id === $request->user()->id, 404);

        return view('mahasiswa.applications.show', [
            'application' => $application->load('program.campus'),
        ]);
    }

    /** Cek kelayakan: kampus sendiri, program dibuka, belum pernah daftar */
    private function guard(Request $request, Program $program)
    {
        abort_unless($program->campus_id === $request->user()->campus_id, 404);

        $existing = $request->user()->applications()->where('program_id', $program->id)->first();
        if ($existing) {
            return redirect()->route('mahasiswa.applications.show', $existing)
                ->with('success', 'Anda sudah mendaftar pada program ini.');
        }

        if (! $program->isAcceptingApplications()) {
            return redirect()->route('mahasiswa.programs.show', $program)
                ->withErrors(['program' => 'Program ini sudah ditutup atau kuotanya penuh.']);
        }

        return null;
    }
}
