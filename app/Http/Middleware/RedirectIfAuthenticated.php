<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                // Debug log
                \Log::info('RedirectIfAuthenticated: User is authenticated', [
                    'path' => $request->path(),
                    'user_id' => $user->id,
                    'has_roles' => $user->roles->count(),
                    'roles' => $user->roles->pluck('name')->toArray()
                ]);
                
                // If user is trying to access login or root page when already authenticated
                if ($request->is('login') || $request->is('/')) {
                    // Check if user has roles
                    if ($user->roles->count() > 0) {
                        // User has roles, use role-based redirect
                        if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
                            return redirect()->route('dashboard');
                        }
                        
                        $dashboardRoute = $user->roles()->first()->dashboard_route ?? null;
                        if ($dashboardRoute && \Route::has($dashboardRoute)) {
                            return redirect()->route($dashboardRoute);
                        }
                    }
                    
                    // Default redirect for all users (including those without roles)
                    return redirect()->route('dashboard');
                }
            }
        }

        return $next($request);
    }
}