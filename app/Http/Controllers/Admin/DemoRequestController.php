<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DemoRequest;

/** Admin CELS melihat permintaan demo dari landing page */
class DemoRequestController extends Controller
{
    public function index()
    {
        return view('admin.demo-requests.index', [
            'requests' => DemoRequest::orderBy('is_handled')->latest()->paginate(20),
        ]);
    }

    public function toggle(DemoRequest $demoRequest)
    {
        $demoRequest->update(['is_handled' => ! $demoRequest->is_handled]);

        return back()->with('success', 'Status permintaan dari '.$demoRequest->institution_name.' diperbarui.');
    }
}
