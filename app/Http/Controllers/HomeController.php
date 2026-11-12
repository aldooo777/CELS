<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    /** Landing page (guest) sesuai desain Stitch "CELS - Landing Page Resmi" */
    public function index()
    {
        return view('home');
    }
}
