<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\CampusRegistration;
use App\Notifications\CampusRegistrationReviewed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/** Admin CELS memverifikasi pendaftaran kampus */
class CampusRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'menunggu');

        return view('admin.registrations.index', [
            'status'        => $status,
            'registrations' => CampusRegistration::with('campus')
                ->when($status !== 'semua', fn ($q) => $q->where('status', $status))
                ->latest()->paginate(15)->withQueryString(),
        ]);
    }

    public function show(CampusRegistration $registration)
    {
        return view('admin.registrations.show', [
            'registration' => $registration->load('campus', 'user', 'reviewer'),
        ]);
    }

    public function document(CampusRegistration $registration)
    {
        abort_unless(Storage::exists($registration->document_path), 404);

        return Storage::response($registration->document_path);
    }

    public function approve(Request $request, CampusRegistration $registration)
    {
        abort_unless($registration->status === 'menunggu', 422, 'Pengajuan ini sudah diproses.');

        DB::transaction(function () use ($request, $registration) {
            $registration->update([
                'status'      => 'disetujui',
                'admin_note'  => $request->input('admin_note'),
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            // Kampus masuk masa uji coba (pintu penjualan)
            $registration->campus->update([
                'status'        => 'uji_coba',
                'trial_ends_at' => now()->addDays(Campus::TRIAL_DAYS),
            ]);

            // Email institusi dianggap terverifikasi oleh Admin CELS
            $registration->user->forceFill(['email_verified_at' => now()])->save();
        });

        $registration->user->notify(new CampusRegistrationReviewed($registration->fresh()));

        return redirect()->route('admin.registrations.index')
            ->with('success', $registration->campus->name.' disetujui dan masuk masa uji coba '.Campus::TRIAL_DAYS.' hari.');
    }

    public function reject(Request $request, CampusRegistration $registration)
    {
        abort_unless($registration->status === 'menunggu', 422, 'Pengajuan ini sudah diproses.');

        $request->validate(['admin_note' => ['required', 'string', 'max:1000']], [
            'admin_note.required' => 'Alasan penolakan wajib diisi.',
        ]);

        DB::transaction(function () use ($request, $registration) {
            $registration->update([
                'status'      => 'ditolak',
                'admin_note'  => $request->input('admin_note'),
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            // Kampus dari daftar PTN kembali bisa didaftarkan
            $registration->campus->update(['status' => 'belum_terdaftar']);
        });

        $registration->user->notify(new CampusRegistrationReviewed($registration->fresh()));

        return redirect()->route('admin.registrations.index')
            ->with('success', 'Pengajuan '.$registration->campus->name.' ditolak.');
    }
}
