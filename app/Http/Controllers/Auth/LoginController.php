<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class LoginController extends Controller
{
    protected int $maxAttempts   = 3;
    protected int $decaySeconds  = 60;

    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);
    
        $credentials = $request->only('username', 'password');
        $key = 'login-attempt:' . $request->ip() . ':' . strtolower($credentials['username']);
    
        if (RateLimiter::tooManyAttempts($key, $this->maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            $menit = floor($seconds / 60);
            $detik = $seconds % 60;
            $waktuTunggu = $menit > 0 ? "{$menit} menit {$detik} detik" : "{$detik} detik";
    
            return back()->withErrors([
                'username' => "🚫 Akun terkunci sementara. Silakan coba lagi dalam {$waktuTunggu}.",
            ])->onlyInput('username');
        }
    
        $user = User::where('username', $credentials['username'])->first();
    
        if (!$user) {
            RateLimiter::hit($key, $this->decaySeconds);
            $sisa = max(0, $this->maxAttempts - RateLimiter::attempts($key));
    
            return back()->withErrors([
                'username' => "Username tidak ditemukan. Sisa percobaan: {$sisa}.",
            ])->onlyInput('username');
        }
    
        if (!$user->is_active) {
            return back()->withErrors([
                'username' => 'Akun Anda dinonaktifkan. Hubungi Administrator.',
            ])->onlyInput('username');
        }
    
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            RateLimiter::clear($key);
            $request->session()->regenerate();
    
            // ==========================================
            // REDIRECT VIA HALAMAN PERANTARA
            // Ini mencegah back button menampilkan halaman login
            // ==========================================
            $role = Auth::user()->role;
            $targetUrl = match ($role) {
                'admin' => route('admin.dashboard'),
                'bidan' => route('bidan.dashboard'),
                'kader' => route('kader.dashboard'),
                default => route('home'),
            };
    
            return redirect()->route('login.success', ['to' => $targetUrl]);
        }
    
        RateLimiter::hit($key, $this->decaySeconds);
        $sisa = max(0, $this->maxAttempts - RateLimiter::attempts($key));
    
        if ($sisa <= 0) {
            return back()->withErrors([
                'username' => "🚫 Akun terkunci sementara selama {$this->decaySeconds} detik karena 3x percobaan gagal.",
            ])->onlyInput('username');
        }
    
        return back()->withErrors([
            'username' => "Username atau password salah. Sisa percobaan: {$sisa}.",
        ])->onlyInput('username');
    }
    
    /**
     * Halaman perantara setelah login sukses.
     * Tugasnya: replace history entry halaman login dengan URL dashboard.
     */
    public function loginSuccess(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
    
        $target = $request->query('to', route('home'));
    
        return view('auth.login-success', ['target' => $target]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    
        // Redirect dengan parameter khusus untuk mendeteksi logout
        return redirect()->route('home')
            ->with('success', 'Anda berhasil keluar dari sistem.')
            ->with('logout_time', now()->timestamp)
            ->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0, private',
                'Pragma'        => 'no-cache',
                'Expires'       => 'Sat, 01 Jan 2000 00:00:00 GMT',
            ]);
    }

    /**
     * Logout via AJAX (dipanggil dari JavaScript saat user klik Back di dashboard)
     */
    public function logoutAjax(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success'  => true,
            'message'  => 'Anda berhasil keluar dari sistem.',
            'redirect' => route('login'),
        ]);
    }

    private function redirectByRole(string $role)
    {
        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'bidan' => redirect()->route('bidan.dashboard'),
            'kader' => redirect()->route('kader.dashboard'),
            default => redirect()->route('home'),
        };
    }
}