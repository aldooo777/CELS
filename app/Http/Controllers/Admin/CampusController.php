<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Daftar kampus + pengelolaan status uji coba / langganan */
class CampusController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'bergabung');
        $search = $request->query('q');

        $campuses = Campus::withCount(['programs', 'students'])
            ->when($status === 'bergabung', fn ($q) => $q->whereIn('status', ['uji_coba', 'berlangganan', 'nonaktif']))
            ->when($status !== 'bergabung' && $status !== 'semua', fn ($q) => $q->where('status', $status))
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.campuses.index', compact('campuses', 'status', 'search'));
    }

    public function updateSubscription(Request $request, Campus $campus)
    {
        $data = $request->validate([
            'status'               => ['required', Rule::in(['uji_coba', 'berlangganan', 'nonaktif'])],
            'trial_ends_at'        => ['nullable', 'date'],
            'subscription_ends_at' => ['nullable', 'date'],
        ]);

        $campus->update($data);

        return back()->with('success', 'Status '.$campus->name.' diperbarui menjadi '.$campus->statusLabel().'.');
    }
}
