<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/** Mengarahkan pengguna ke dashboard sesuai perannya */
class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        return redirect()->route($request->user()->homeRoute());
    }
}
