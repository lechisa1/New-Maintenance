<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Show the application's login form.
     */
    public function showLoginForm()
    {
        return view('pages.auth.signin');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // Check if the user has too many login attempts
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            $request->session()->regenerate();

            $this->clearLoginAttempts($request);
// dd([
//     'session_id' => session()->getId(),
//     'auth_id' => auth()->id(),
//     'db_user_id' => \DB::table('sessions')->where('id', session()->getId())->value('user_id'),
// ]);

            // Log successful login
            Log::info('User logged in', [
                'user_id' => Auth::id(),
                'email' => $request->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'has_roles' => Auth::user()->roles->count()
            ]);

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful, increment login attempts
        $this->incrementLoginAttempts($request);

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    /**
     * Validate the user login request.
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'remember' => ['nullable', 'boolean'],
        ]);
    }

    /**
     * Attempt to log the user into the application.
     */
    protected function attemptLogin(Request $request)
    {
        return Auth::attempt(
            $this->credentials($request),
            $request->filled('remember')
        );
    }

    /**
     * Get the needed authorization credentials from the request.
     */
    protected function credentials(Request $request)
    {
        return $request->only('email', 'password');
    }

    /**
     * Send the response after the user was authenticated.
     */
    protected function sendLoginResponse(Request $request)
    {
       

        // Store login activity
        Auth::user()->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip()
        ]);

        // Debug log
        Log::info('Login response', [
            'user_id' => Auth::id(),
            'has_roles' => Auth::user()->roles->count(),
            'roles_list' => Auth::user()->roles->pluck('name')->toArray(),
            'redirect_path' => $this->redirectPath()
        ]);

        // Use intended redirect - this will check session for 'url.intended'
       return redirect()->route('dashboard');

    }

    /**
     * Get the post login redirect path.
     */
    protected function redirectPath()
    {
        $user = Auth::user();
        
        // Log role information for debugging
        Log::info('Checking redirect path for user', [
            'user_id' => $user->id,
            'has_roles' => $user->roles->count(),
            'roles' => $user->roles->pluck('name')->toArray()
        ]);
        
        // Check if user has ANY roles
        if ($user->roles->count() > 0) {
            // Role-based redirect only if user has roles
            if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
                return '/dashboard'; // or '/admin/dashboard' if you create that route
            }

            $dashboardRoute = $user->roles()->first()->dashboard_route ?? null;
            
            if ($dashboardRoute && \Route::has($dashboardRoute)) {
                return route($dashboardRoute);
            }
        }
        
        // Default redirect for users without roles
        return '/dashboard';
    }

    /**
     * Determine if the user has too many failed login attempts.
     */
    protected function hasTooManyLoginAttempts(Request $request)
    {
        return RateLimiter::tooManyAttempts(
            $this->throttleKey($request),
            $this->maxAttempts()
        );
    }

    /**
     * Increment the login attempts for the user.
     */
    protected function incrementLoginAttempts(Request $request)
    {
        RateLimiter::hit(
            $this->throttleKey($request),
            $this->decayMinutes() * 60
        );
    }

    /**
     * Clear the login locks for the given user credentials.
     */
    protected function clearLoginAttempts(Request $request)
    {
        RateLimiter::clear($this->throttleKey($request));
    }

    /**
     * Get the throttle key for the given request.
     */
    protected function throttleKey(Request $request)
    {
        return Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());
    }

    /**
     * Get the maximum number of attempts to allow.
     */
    protected function maxAttempts()
    {
        return 5;
    }

    /**
     * Get the number of minutes to throttle for.
     */
    protected function decayMinutes()
    {
        return 15;
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        // Log logout activity
        if (Auth::check()) {
            Log::info('User logged out', [
                'user_id' => Auth::id(),
                'email' => Auth::user()->email,
                'ip' => $request->ip()
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Show the application's logout form (for confirmation).
     */
    public function showLogoutForm()
    {
        return view('pages.auth.logout-confirm');
    }
}