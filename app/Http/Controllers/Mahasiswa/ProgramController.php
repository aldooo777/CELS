<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;

/** Mahasiswa melihat program yang diposting kampusnya sendiri */
class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $type   = $request->query('type');
        $search = $request->query('q');

        $programs = Program::available()
            ->where('campus_id', $request->user()->campus_id)
            ->when($type, fn ($q) => $q->where('type', $type))
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->orderBy('deadline')->paginate(12)->withQueryString();

        $appliedIds = $request->user()->applications()->pluck('program_id')->all();

        return view('mahasiswa.programs.index', [
            'programs'   => $programs,
            'appliedIds' => $appliedIds,
            'types'      => Program::TYPES,
            'type'       => $type,
            'search'     => $search,
        ]);
    }

    public function show(Request $request, Program $program)
    {
        abort_unless($program->campus_id === $request->user()->campus_id, 404);

        return view('mahasiswa.programs.show', [
            'program'     => $program->load('campus'),
            'application' => $request->user()->applications()->where('program_id', $program->id)->first(),
        ]);
    }
}
