<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Replaces legacy requireRole() calls scattered at the top of each PHP file.
 * Usage in routes: ->middleware('role:Admin') or ->middleware('role:Admin,Teacher')
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_unless($user && in_array($user->role, $roles, true), 403);

        return $next($request);
    }
}
