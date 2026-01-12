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

                <!-- Form -->
                <form>
                    <div class="space-y-5">

                        <!-- Email -->
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Email <span class="text-error-500">*</span>
                            </label>
                            <input
                                type="email"
                                name="email"
                                placeholder="info@gmail.com"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                                       text-gray-800 placeholder:text-gray-400 shadow-theme-xs
                                       focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10
                                       dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                            />
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Password <span class="text-error-500">*</span>
                            </label>
                            <div x-data="{ showPassword: false }" class="relative">
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    placeholder="Enter your password"
                                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 pr-11
                                           text-sm text-gray-800 placeholder:text-gray-400 shadow-theme-xs
                                           focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10
                                           dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                />

                                <span
                                    @click="showPassword = !showPassword"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 cursor-pointer text-gray-500 dark:text-gray-400"
                                >
                                    👁
                                </span>
                            </div>
                        </div>

                        <!-- Remember + Forgot -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center text-sm text-gray-700 dark:text-gray-400">
                                <input type="checkbox" class="mr-2 rounded border-gray-300 dark:border-gray-700">
                                Keep me logged in
                            </label>

                            <a href="/reset-password" class="text-sm text-brand-500 hover:text-brand-600">
                                Forgot password?
                            </a>
                        </div>

                        <!-- Button -->
                        <button
                            type="submit"
                            class="flex w-full items-center justify-center rounded-lg bg-brand-500 px-4 py-3
                                   text-sm font-medium text-white shadow-theme-xs transition
                                   hover:bg-brand-600"
                        >
                            Sign In
                        </button>

                    </div>
                </form>

                <!-- Signup -->
                <div class="mt-5 text-center">
                    <p class="text-sm text-gray-700 dark:text-gray-400">
                        Don't have an account?
                        <a href="/signup" class="text-brand-500 hover:text-brand-600">
                            Sign Up
                        </a>
                    </p>
                </div>

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
