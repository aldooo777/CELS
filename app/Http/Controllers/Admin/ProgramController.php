<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Campus;
use App\Models\Program;
use Illuminate\Http\Request;

/** Admin CELS melihat seluruh program dari semua kampus (hanya lihat) */
class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->query('q');
        $campusId = $request->query('kampus');
        $type     = $request->query('jenis');
        $status   = $request->query('status');

        $programs = Program::with('campus')
            ->withCount([
                'applications',
                'applications as approved_count' => fn ($q) => $q->where('status', Application::STATUS_DISETUJUI),
            ])
            ->when($search, fn ($q) => $q->where('title', 'ilike', "%{$search}%"))
            ->when($campusId, fn ($q) => $q->where('campus_id', $campusId))
            ->when($type, fn ($q) => $q->where('type', $type))
            ->when($status === 'dibuka', fn ($q) => $q->available())
            ->when($status === 'ditutup', fn ($q) => $q->where(fn ($w) => $w
                ->where('is_open', false)
                ->orWhereDate('deadline', '<', now()->toDateString())))
            ->latest()
            ->paginate(20)->withQueryString();

        return view('admin.programs.index', [
            'programs' => $programs,
            'campuses' => Campus::joined()->orderBy('name')->get(['id', 'name']),
            'types'    => Program::TYPES,
            'search'   => $search,
            'campusId' => $campusId,
            'type'     => $type,
            'status'   => $status,
        ]);
    }

    public function show(Program $program)
    {
        return view('admin.programs.show', [
            'program'       => $program->load('campus', 'creator'),
            'applications'  => $program->applications()->with('student')->latest()->get(),
            'approvedCount' => $program->approvedCount(),
        ]);
    }
}
