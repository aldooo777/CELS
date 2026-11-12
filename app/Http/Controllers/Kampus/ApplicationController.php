<?php

namespace App\Http\Controllers\Kampus;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Notifications\ApplicationStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/** Admin kampus melihat pendaftar lalu menyetujui / menolak */
class ApplicationController extends Controller
{
    public function show(Request $request, Application $application)
    {
        $this->authorizeCampus($request, $application);

        // Saat pertama kali dibuka, status otomatis menjadi "Diproses"
        if ($application->status === Application::STATUS_DIAJUKAN) {
            $application->update([
                'status'            => Application::STATUS_DIPROSES,
                'status_changed_at' => now(),
            ]);
            $application->student->notify(new ApplicationStatusChanged($application));
        }

        return view('kampus.applications.show', [
            'application' => $application->load('program', 'student.campus'),
        ]);
    }

    public function updateStatus(Request $request, Application $application)
    {
        $this->authorizeCampus($request, $application);

        $data = $request->validate([
            'status'     => ['required', Rule::in([Application::STATUS_DIPROSES, Application::STATUS_DISETUJUI, Application::STATUS_DITOLAK])],
            'admin_note' => ['nullable', 'string', 'max:2000', 'required_if:status,'.Application::STATUS_DITOLAK],
        ], ['admin_note.required_if' => 'Alasan penolakan wajib diisi.']);

        $program = $application->program;

        if ($data['status'] === Application::STATUS_DISETUJUI
            && $application->status !== Application::STATUS_DISETUJUI
            && $program->isQuotaFull()) {
            return back()->withErrors(['status' => 'Kuota program sudah penuh ('.$program->quota.' orang).']);
        }

        if ($application->status === $data['status'] && $application->admin_note === ($data['admin_note'] ?? null)) {
            return back()->with('success', 'Tidak ada perubahan.');
        }

        $application->update([
            ...$data,
            'status_changed_at' => now(),
        ]);

        // Notifikasi email setiap status berubah
        $application->student->notify(new ApplicationStatusChanged($application));

        return redirect()->route('kampus.programs.show', $program)
            ->with('success', 'Status '.$application->student->name.' diubah menjadi '.$application->statusLabel().'.');
    }

    public function download(Request $request, Application $application, string $type)
    {
        $this->authorizeCampus($request, $application);

        $path = $type === 'cv' ? $application->cv_path : $application->document_path;
        abort_unless($path && Storage::exists($path), 404);

        return Storage::response($path);
    }

    private function authorizeCampus(Request $request, Application $application): void
    {
        abort_unless($application->program->campus_id === $request->user()->campus_id, 403);
    }
}
