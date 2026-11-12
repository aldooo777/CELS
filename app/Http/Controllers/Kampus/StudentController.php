<?php

namespace App\Http\Controllers\Kampus;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

/** Opsi verifikasi alternatif: admin kampus memverifikasi mahasiswanya */
class StudentController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'belum');
        $search = $request->query('q');

        $students = $request->user()->campus->students()
            ->when($filter === 'belum', fn ($q) => $q->whereNull('email_verified_at'))
            ->when($filter === 'sudah', fn ($q) => $q->whereNotNull('email_verified_at'))
            ->when($search, fn ($q) => $q->where(fn ($w) => $w->where('name', 'like', "%{$search}%")->orWhere('nim', 'like', "%{$search}%")))
            ->orderBy('name')->paginate(20)->withQueryString();

        return view('kampus.students.index', compact('students', 'filter', 'search'));
    }

    public function verify(Request $request, User $student)
    {
        abort_unless($student->isMahasiswa() && $student->campus_id === $request->user()->campus_id, 403);

        $student->forceFill([
            'email_verified_at'     => $student->email_verified_at ?? now(),
            'verified_by_campus_at' => now(),
        ])->save();

        return back()->with('success', $student->name.' telah diverifikasi.');
    }
}
