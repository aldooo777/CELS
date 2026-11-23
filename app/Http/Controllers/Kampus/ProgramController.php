<?php

namespace App\Http\Controllers\Kampus;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Program;
use App\Rules\MeaningfulText;
use App\Rules\ProgramTitle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Admin kampus memposting & mengelola program PDLK */
class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $posting = in_array($request->query('posting'), ['terbit', 'draf'], true) ? $request->query('posting') : '';
        $base    = $request->user()->campus->programs();

        $counts = [
            ''       => (clone $base)->count(),
            'terbit' => (clone $base)->whereNotNull('published_at')->count(),
            'draf'   => (clone $base)->whereNull('published_at')->count(),
        ];

        $programs = $base
            ->when($posting === 'terbit', fn ($q) => $q->whereNotNull('published_at'))
            ->when($posting === 'draf', fn ($q) => $q->whereNull('published_at'))
            ->withCount([
                'applications',
                'applications as pending_count'  => fn ($q) => $q->whereIn('status', [Application::STATUS_DIAJUKAN, Application::STATUS_DIPROSES]),
                'applications as approved_count' => fn ($q) => $q->where('status', Application::STATUS_DISETUJUI),
            ])
            ->latest()->paginate(10)->withQueryString();

        return view('kampus.programs.index', compact('programs', 'posting', 'counts'));
    }

    public function create()
    {
        return view('kampus.programs.create', ['program' => new Program(['is_open' => true, 'audience' => 'internal'])]);
    }

    public function store(Request $request)
    {
        $data    = $this->validated($request);
        $publish = $request->input('action') === 'publish';

        $program = $request->user()->campus->programs()->create([
            ...$data,
            'created_by'   => $request->user()->id,
            'published_at' => $publish ? now() : null,
        ]);

        return redirect()->route('kampus.programs.show', $program)->with('success', $publish
            ? 'Program berhasil diposting dan sudah tampil untuk mahasiswa.'
            : 'Program disimpan sebagai draf. Mahasiswa belum bisa melihatnya.');
    }

    public function show(Request $request, Program $program)
    {
        $this->authorizeCampus($request, $program);

        $status = $request->query('status');

        return view('kampus.programs.show', [
            'program'      => $program,
            'status'       => $status,
            'applications' => $program->applications()->with('student.campus')
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

        $data   = $this->validated($request, $program);
        $action = $request->input('action');            // publish | draft | null (simpan saja)

        if ($action === 'publish' && ! $program->isPublished()) {
            $data['published_at'] = now();
        } elseif ($action === 'draft' && $program->isPublished()) {
            if ($program->applications()->exists()) {
                return back()->withInput()->withErrors(['action' => 'Program yang sudah memiliki pendaftar tidak bisa dijadikan draf. Tutup pendaftarannya saja.']);
            }
            $data['published_at'] = null;
        }

        $program->update($data);

        $message = match (true) {
            $action === 'publish' => 'Program berhasil diposting dan sudah tampil untuk mahasiswa.',
            $action === 'draft'   => 'Program dijadikan draf dan disembunyikan dari mahasiswa.',
            default               => 'Program diperbarui.',
        };

        return redirect()->route('kampus.programs.show', $program)->with('success', $message);
    }

    /** Tombol "Posting sekarang" pada halaman detail draf */
    public function publish(Request $request, Program $program)
    {
        $this->authorizeCampus($request, $program);

        if (! $program->isPublished()) {
            $program->update(['published_at' => now()]);
        }

        return redirect()->route('kampus.programs.show', $program)->with('success', 'Program berhasil diposting dan sudah tampil untuk mahasiswa.');
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
            'title'        => ['required', 'string', 'max:255', new ProgramTitle],
            'type'         => ['required', Rule::in(array_keys(Program::TYPES))],
            'description'  => ['required', 'string', 'max:5000', new MeaningfulText(10)],
            'quota'        => ['required', 'integer', 'min:1', 'max:10000'],
            // Saat membuat program baru, deadline tidak boleh di masa lalu
            'deadline'     => array_filter(['required', 'date', $program ? null : 'after_or_equal:today']),
            'requirements' => ['required', 'string', 'max:3000', new MeaningfulText(5)],
            'partner_info' => ['nullable', 'string', 'max:2000'],
            'audience'     => ['required', Rule::in(array_keys(Program::AUDIENCES))],
            'action'       => ['nullable', Rule::in(['publish', 'draft'])],
        ]);

        unset($data['action']);

        $data['is_open'] = $request->boolean('is_open');

        return $data;
    }

    private function authorizeCampus(Request $request, Program $program): void
    {
        abort_unless($program->campus_id === $request->user()->campus_id, 403);
    }
}
