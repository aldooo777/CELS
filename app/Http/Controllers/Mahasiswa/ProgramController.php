<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\Program;
use Illuminate\Http\Request;

/** Mahasiswa melihat program dari kampusnya sendiri dan dari kampus lain */
class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $user   = $request->user();
        $type   = $request->query('type');
        $search = $request->query('q');
        $scope  = $request->query('kampus', 'semua');   // semua | saya | bisa | {id kampus}

        // Hanya kampus yang uji coba / langganannya masih berlaku
        $activeCampusIds = Campus::joined()->get()->filter->isActive()->pluck('id');

        $programs = Program::available()
            ->whereIn('campus_id', $activeCampusIds)
            ->with('campus')
            ->when($scope === 'saya', fn ($q) => $q->where('campus_id', $user->campus_id))
            ->when($scope === 'bisa', fn ($q) => $q->openTo($user))
            ->when(ctype_digit((string) $scope), fn ($q) => $q->where('campus_id', $scope))
            ->when($type, fn ($q) => $q->where('type', $type))
            ->when($search, fn ($q) => $q->where('title', 'ilike', "%{$search}%"))
            ->orderBy('deadline')->paginate(12)->withQueryString();

        return view('mahasiswa.programs.index', [
            'programs'   => $programs,
            'appliedIds' => $user->applications()->pluck('program_id')->all(),
            'campuses'   => Campus::whereIn('id', $activeCampusIds)->whereHas('programs', fn ($q) => $q->available())->orderBy('name')->get(),
            'types'      => Program::TYPES,
            'type'       => $type,
            'search'     => $search,
            'scope'      => $scope,
        ]);
    }

    public function show(Request $request, Program $program)
    {
        // Draf atau kampus yang tidak aktif tidak bisa dibuka mahasiswa
        abort_unless($program->isPublished() && $program->campus->isActive(), 404);

        return view('mahasiswa.programs.show', [
            'program'     => $program,
            'application' => $request->user()->applications()->where('program_id', $program->id)->first(),
        ]);
    }
}
