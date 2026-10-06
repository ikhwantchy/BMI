@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
{{-- Brand color top stripe --}}
<div class="h-1 flex w-full fixed top-0 left-0 z-50">
    <div class="h-full flex-1 bg-[#009a4c]"></div>
    <div class="h-full w-24 bg-[#e4c85b]"></div>
    <div class="h-full w-24 bg-[#00a1e8]"></div>
</div>

<div class="min-h-screen flex items-center justify-center p-6 bg-white">
    <div class="w-full max-w-sm">

        {{-- Brand Header with Logo Text --}}
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-block">
                <img src="{{ asset('images/logo-kopsyah-bmi-new-text.png') }}"
                     alt="Logo Koperasi Syariah BMI"
                     class="h-16 mx-auto object-contain">
            </a>
        </div>

        {{-- Login Box (Pure White, 1px Border, No Shadow, Sharp) --}}
        <div class="bg-white border border-gray-200 p-8">
            <div class="mb-6 pb-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Masuk Akun</h2>
                </div>
                <span class="w-2 h-2 bg-[#009a4c]"></span>
            </div>

            <form method="POST" action="{{ route('login') }}" id="login-form" class="space-y-4">
                @csrf

                {{-- Username --}}
                <div>
                    <label for="username" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                        Username atau Email
                    </label>
                    <input type="text"
                           id="username"
                           name="username"
                           value="{{ old('username') }}"
                           autocomplete="username"
                           autofocus
                           required
                           class="w-full px-3 py-2 text-sm border {{ $errors->has('username') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} bg-white text-gray-900 focus:outline-none focus:border-[#009a4c] transition-colors">
                    @error('username')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Password
                        </label>
                    </div>
                    <input type="password"
                           id="password"
                           name="password"
                           autocomplete="current-password"
                           required
                           placeholder="••••••••"
                           class="w-full px-3 py-2 text-sm border {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} bg-white text-gray-900 focus:outline-none focus:border-[#009a4c] transition-colors">
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Cloudflare Turnstile Widget --}}
                <div class="pt-1 flex flex-col items-center justify-center">
                    <div class="cf-turnstile"
                         data-sitekey="{{ config('services.turnstile.key', '0x4AAAAAAFOAB8-_ECMbKB4G') }}"
                         data-theme="light"
                         data-size="normal"></div>
                    @error('cf-turnstile-response')
                        <p class="mt-1.5 text-xs text-red-600 text-center font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit Button --}}
                <div class="pt-1">
                    <button type="submit"
                            id="login-btn"
                            class="w-full py-2.5 px-4 bg-[#009a4c] hover:bg-[#007d3e] text-white text-xs font-semibold uppercase tracking-wider transition-colors focus:outline-none focus:ring-1 focus:ring-[#009a4c] cursor-pointer">
                        Login
                    </button>
                </div>
            </form>
        </div>

        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

        <p class="text-center text-xs text-gray-400 mt-6 tracking-wide">
            &copy; {{ date('Y') }} Koperasi Syariah BMI &bull; Melayani dengan Hati Nurani
        </p>
    </div>
</div>
@endsection
