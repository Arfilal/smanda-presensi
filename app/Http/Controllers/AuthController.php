<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan halaman login (sesuai referensi UI Login)
    public function showLogin()
    {
        return view('auth.login');
    }

    // Proses autentikasi login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Redirect berdasarkan role masing-masing portal
            switch ($user->role) {
                case 'operator':
                    return redirect()->route('operator.dashboard');
                case 'kesiswaan':
                    return redirect()->route('kesiswaan.dashboard');
                case 'wali_kelas':
                    return redirect()->route('walikelas.dashboard');
                case 'guru_mapel':
                    return redirect()->route('gurumapel.dashboard');
                case 'siswa':
                    return redirect()->route('siswa.dashboard');
                default:
                    Auth::logout();
                    return redirect()->route('login')->withErrors(['email' => 'Role akun tidak dikenali.']);
            }
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    // Proses logout akun
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}