<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin kampus hanya bisa memakai fitur jika kampusnya sudah diverifikasi
 * dan masa uji coba / langganannya masih berlaku.
 */
class EnsureCampusActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $campus = $request->user()?->campus;

        if (! $campus || ! $campus->isActive()) {
            return redirect()->route('kampus.status');
        }

        return $next($request);
    }
}
