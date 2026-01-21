@extends('layouts.fullscreen-layout')

@section('content')
<div class="relative z-1 bg-white p-6 sm:p-0 dark:bg-gray-900">
    <div class="relative flex h-screen w-full flex-col justify-center lg:flex-row dark:bg-gray-900">

        <!-- Login Form -->
        <div class="flex w-full flex-1 items-center justify-center lg:w-1/2">

            <!-- Card -->
            <div
                class="w-full max-w-md rounded-2xl bg-white p-8 shadow-lg ring-1 ring-gray-200
                       dark:bg-gray-900 dark:ring-gray-800 dark:shadow-[0_10px_40px_rgba(0,0,0,0.6)]"
            >

                <!-- Logo & Title -->
                <div class="mb-6 text-center">
                    <img
                        src="https://products.aii.et/assets/img/AII-logo.png"
                        alt="AII Logo"
                        class="mx-auto mb-4 h-16 sm:h-18 w-auto drop-shadow-md"
                    />
                    <h1 class="text-title-sm sm:text-title-md mb-2 font-semibold text-gray-800 dark:text-white/90">
                        Sign In
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Enter your email and password to sign in!
                    </p>
                </div>

                <!-- Error Messages -->
                @if($errors->any())
                    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                        <div class="flex items-center">
                            <i class="bi bi-exclamation-circle me-3 text-red-500 dark:text-red-400"></i>
                            <div>
                                @foreach($errors->all() as $error)
                                    <p class="text-sm text-red-700 dark:text-red-300">{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('status'))
                    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
                        <div class="flex items-center">
                            <i class="bi bi-check-circle me-3 text-green-500 dark:text-green-400"></i>
                            <p class="text-sm text-green-700 dark:text-green-300">{{ session('status') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="space-y-5">

                        <!-- Email -->
                        <div>
                            <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Email <span class="text-error-500">*</span>
                            </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                placeholder="info@gmail.com"
                                class="h-11 w-full rounded-lg border @error('email') border-red-300 @else border-gray-300 @enderror
                                       bg-transparent px-4 text-sm text-gray-800 placeholder:text-gray-400 shadow-theme-xs
                                       focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10
                                       dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500"
                            />
                            @error('email')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Password <span class="text-error-500">*</span>
                            </label>
                            <div x-data="{ showPassword: false }" class="relative">
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    id="password"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="Enter your password"
                                    class="h-11 w-full rounded-lg border @error('password') border-red-300 @else border-gray-300 @enderror
                                           bg-transparent px-4 pr-11 text-sm text-gray-800 placeholder:text-gray-400 shadow-theme-xs
                                           focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10
                                           dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500"
                                />

                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                                >
                                    <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Remember + Forgot -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center text-sm text-gray-700 dark:text-gray-400">
                                <input 
                                    type="checkbox" 
                                    name="remember" 
                                    id="remember"
                                    {{ old('remember') ? 'checked' : '' }}
                                    class="mr-2 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-700"
                                >
                                Keep me logged in
                            </label>

                            @if(Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm text-brand-500 hover:text-brand-600">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        <!-- Button -->
                        <button
                            type="submit"
                            class="flex w-full items-center justify-center rounded-lg bg-brand-500 px-4 py-3
                                   text-sm font-medium text-white shadow-theme-xs transition
                                   hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2"
                        >
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Sign In
                        </button>

                    </div>
                </form>

                <!-- Signup Link -->
                @if(Route::has('register'))
                    <div class="mt-5 text-center">
                        <p class="text-sm text-gray-700 dark:text-gray-400">
                            Don't have an account?
                            <a href="{{ route('register') }}" class="text-brand-500 hover:text-brand-600">
                                Sign Up
                            </a>
                        </p>
                    </div>
                @endif

            </div>
        </div>

        <!-- Theme Toggle -->
        <div class="fixed bottom-6 right-6 z-50">
            <button
                class="inline-flex size-14 items-center justify-center rounded-full bg-brand-500
                       text-white transition hover:bg-brand-600"
                @click.prevent="$store.theme.toggle()"
            >
                🌓
            </button>
        </div>

    </div>
</div>
@endsection
