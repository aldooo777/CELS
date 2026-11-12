<?php

namespace App\Http\Controllers\Kampus;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Admin kampus memposting & mengelola program PDLK */
class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $programs = $request->user()->campus->programs()
            ->withCount([
                'applications',
                'applications as pending_count'  => fn ($q) => $q->whereIn('status', [Application::STATUS_DIAJUKAN, Application::STATUS_DIPROSES]),
                'applications as approved_count' => fn ($q) => $q->where('status', Application::STATUS_DISETUJUI),
            ])
            ->latest()->paginate(10);

        return view('kampus.programs.index', compact('programs'));
    }

    public function create()
    {
        return view('kampus.programs.create', ['program' => new Program(['is_open' => true])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $program = $request->user()->campus->programs()->create([
            ...$data,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('kampus.programs.show', $program)->with('success', 'Program berhasil diposting.');
    }

    public function show(Request $request, Program $program)
    {
        $this->authorizeCampus($request, $program);

        $status = $request->query('status');

        return view('kampus.programs.show', [
            'program'      => $program,
            'status'       => $status,
            'applications' => $program->applications()->with('student')
                ->when($status, fn ($q) => $q->where('status', $status))
                ->latest()->paginate(20)->withQueryString(),
            'approvedCount' => $program->approvedCount(),
        ]);
    }

    public function edit(Request $request, Program $program)
    {
        $this->authorizeCampus($request, $program);

        return view('kampus.programs.edit', compact('program'));
    }

    public function update(Request $request, Program $program)
    {
        $this->authorizeCampus($request, $program);

        $program->update($this->validated($request, $program));

        return redirect()->route('kampus.programs.show', $program)->with('success', 'Program diperbarui.');
    }

    public function destroy(Request $request, Program $program)
    {
        $this->authorizeCampus($request, $program);

        if ($program->applications()->exists()) {
            // Program yang sudah punya pendaftar hanya ditutup, tidak dihapus
            $program->update(['is_open' => false]);

            return redirect()->route('kampus.programs.index')->with('success', 'Program sudah memiliki pendaftar, sehingga hanya ditutup.');
        }

        $program->delete();

        return redirect()->route('kampus.programs.index')->with('success', 'Program dihapus.');
    }

    private function validated(Request $request, ?Program $program = null): array
    {
        $data = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'type'         => ['required', Rule::in(array_keys(Program::TYPES))],
            'description'  => ['required', 'string'],
            'quota'        => ['required', 'integer', 'min:1', 'max:10000'],
            // Saat membuat program baru, deadline tidak boleh di masa lalu
            'deadline'     => array_filter(['required', 'date', $program ? null : 'after_or_equal:today']),
            'requirements' => ['required', 'string'],
            'partner_info' => ['nullable', 'string', 'max:2000'],
        ]);

        $data['is_open'] = $request->boolean('is_open');

        return $data;
    }

    private function authorizeCampus(Request $request, Program $program): void
    {
        abort_unless($program->campus_id === $request->user()->campus_id, 403);
    }
}
