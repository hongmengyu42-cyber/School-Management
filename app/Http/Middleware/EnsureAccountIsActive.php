<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Replaces the legacy "registration goes into Pending status, admin approves"
 * flow. A logged-in user whose status isn't Active gets bounced to a holding
 * page instead of into the app, on every request (not just at login).
 */
class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->status !== 'Active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['username' => 'Your account is pending admin approval or has been suspended.']);
        }

        return $next($request);
    }
}
