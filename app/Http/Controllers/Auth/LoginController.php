<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'username'              => ['required', 'string'],
            'password'              => ['required', 'string'],
            'cf-turnstile-response' => [new \App\Rules\Turnstile],
        ]);

        // Rate limiting: max 5 percobaan per menit
        $key = 'login.' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'username' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        $loginField = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginField => $request->username,
            'password'  => $request->password,
            'status'    => 'active',
        ];

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($key, 60);

            $this->auditService->logFailedLogin($request->username, 'Username atau password salah');

            throw ValidationException::withMessages([
                'username' => 'Username atau password salah.',
            ]);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        $this->auditService->logLogin(Auth::id());

        return redirect()->intended(route('members.index'));
    }

    public function logout(Request $request): RedirectResponse
    {
        if ($userId = Auth::id()) {
            $this->auditService->logLogout($userId);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
