<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Flatten the permissions array and split pipe-separated values
        $allPermissions = [];
        foreach ($permissions as $permission) {
            if (is_string($permission) && str_contains($permission, '|')) {
                $allPermissions = array_merge($allPermissions, explode('|', $permission));
            } else {
                $allPermissions[] = $permission;
            }
        }

        foreach ($allPermissions as $permission) {
            if ($request->user()->hasPermissionTo($permission)) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized action.');
    }
}
