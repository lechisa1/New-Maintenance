<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Custom route model binding for UUID
        Route::bind('role', function ($value) {
            return \App\Models\Role::where('id', $value)
                ->orWhere('name', $value)
                ->firstOrFail();
        });

        Route::bind('permission', function ($value) {
            return \App\Models\Permission::where('id', $value)
                ->orWhere('name', $value)
                ->firstOrFail();
        });
    }
}