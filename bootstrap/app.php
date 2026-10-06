<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() !== 419) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sesi kedaluwarsa. Silakan muat ulang halaman.',
                ], 419);
            }

            if ($request->is('login') || $request->routeIs('login')) {
                return redirect()->route('login')
                    ->withInput($request->except('password', '_token', 'cf-turnstile-response'))
                    ->with('warning', 'Sesi login telah diperbarui demi keamanan. Silakan masukkan password dan coba masuk kembali.');
            }

            return redirect()->back(fallback: route('login'))
                ->withInput($request->except('password', '_token', 'cf-turnstile-response'))
                ->with('warning', 'Sesi Anda telah kedaluwarsa demi keamanan. Halaman telah disegarkan dengan sesi baru, silakan coba kembali.');
        });
    })->create();

