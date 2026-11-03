<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Jika salah role, kembalikan ke dashboard masing-masing
        switch ($user->role) {
            case 'operator': return redirect()->route('operator.dashboard');
            case 'kesiswaan': return redirect()->route('kesiswaan.dashboard');
            case 'wali_kelas': return redirect()->route('walikelas.dashboard');
            case 'guru_mapel': return redirect()->route('gurumapel.dashboard');
            case 'siswa': return redirect()->route('siswa.dashboard');
            default: return redirect()->route('login');
        }
    }
}