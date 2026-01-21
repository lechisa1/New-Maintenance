@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <i class="bi bi-box-arrow-right text-5xl text-gray-400 mb-4"></i>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white">
                Sign Out
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Are you sure you want to sign out?
            </p>
        </div>
        
        <div class="mt-8 space-y-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="bi bi-box-arrow-right"></i>
                    </span>
                    Yes, Sign Out
                </button>
            </form>
            
            <a href="{{ url()->previous() }}" 
               class="group relative w-full flex justify-center py-3 px-4 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">
                Cancel
            </a>
        </div>
    </div>
</div>
@endsection