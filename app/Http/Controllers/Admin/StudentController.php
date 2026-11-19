<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\User;
use Illuminate\Http\Request;

/** Admin CELS melihat seluruh mahasiswa di semua kampus */
class StudentController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->query('q');
        $campusId = $request->query('kampus');
        $status   = $request->query('status');

        $students = User::where('role', User::ROLE_MAHASISWA)
            ->with('campus')
            ->withCount('applications')
            ->when($search, fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'ilike', "%{$search}%")
                ->orWhere('nim', 'ilike', "%{$search}%")
                ->orWhere('email', 'ilike', "%{$search}%")))
            ->when($campusId, fn ($q) => $q->where('campus_id', $campusId))
            ->when($status === 'terverifikasi', fn ($q) => $q->whereNotNull('email_verified_at'))
            ->when($status === 'belum', fn ($q) => $q->whereNull('email_verified_at'))
            ->orderBy('name')
            ->paginate(20)->withQueryString();

        return view('admin.students.index', [
            'students' => $students,
            'campuses' => Campus::joined()->orderBy('name')->get(['id', 'name']),
            'search'   => $search,
            'campusId' => $campusId,
            'status'   => $status,
        ]);
    }

    public function show(User $student)
    {
        abort_unless($student->isMahasiswa(), 404);

        return view('admin.students.show', [
            'student'      => $student->load('campus'),
            'applications' => $student->applications()->with('program.campus')->latest()->get(),
        ]);
    }
}
