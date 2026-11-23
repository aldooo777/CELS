<?php

namespace App\Http\Controllers;

use App\Models\Program;

class HomeController extends Controller
{
    /** Landing page (guest) + program terbaru dari semua kampus */
    public function index()
    {
        $programs = Program::available()                       // dibuka & belum lewat deadline
            ->whereHas('campus', fn ($q) => $q->joined())      // kampus sudah disetujui
            ->with('campus')
            ->latest('published_at')
            ->get()
            ->filter(fn ($p) => $p->campus->isActive())        // uji coba / langganan masih berlaku
            ->take(6);

        return view('home', compact('programs'));
    }
}
