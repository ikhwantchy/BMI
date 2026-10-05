<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class Turnstile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Bypass during automated testing
        if (app()->environment('testing')) {
            return;
        }

        $secret = config('services.turnstile.secret');
        if (empty($secret)) {
            return;
        }

        if (empty($value)) {
            $fail('Verifikasi keamanan Cloudflare Turnstile wajib diselesaikan.');
            return;
        }

        try {
            $response = Http::asForm()->timeout(5)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret'   => $secret,
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

            if (!$response->successful() || !$response->json('success')) {
                $fail('Verifikasi keamanan Cloudflare gagal atau telah kedaluwarsa. Silakan centang ulang.');
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
