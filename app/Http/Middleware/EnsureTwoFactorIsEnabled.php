<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fortify's twoFactorAuthentication() feature is opt-in by design — a user
 * can log in forever without ever enabling it. The legacy system made 2FA
 * mandatory for Admin/Teacher (Step 1 audit), so this middleware closes
 * that gap: any Admin/Teacher without a *confirmed* 2FA setup gets routed
 * to the setup page on every request until they complete it.
 *
 * Deliberately checks two_factor_confirmed_at (not just two_factor_secret)
 * — Fortify sets the secret as soon as setup starts, but confirmed_at only
 * gets set once the user has actually verified a code, meaning they've
 * proven they can generate valid codes rather than just started the flow.
 */
class EnsureTwoFactorIsEnabled
{
    /**
     * Matched against the request path rather than route name — Fortify's
     * own two-factor management endpoints (enable/confirm/qr-code/recovery
     * codes) aren't consistently named across versions, but their URIs are
     * stable and documented, so matching on path is the more reliable check.
     */
    private const EXEMPT_PATH_PATTERNS = [
        'two-factor-setup',
        'two-factor-challenge',
        'user/two-factor-authentication',
        'user/confirmed-two-factor-authentication',
        'user/two-factor-qr-code',
        'user/two-factor-recovery-codes',
        'user/two-factor-secret-key',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $requiresTwoFactor = $user && in_array($user->role, ['Admin', 'Teacher'], true);

        if ($requiresTwoFactor && !$user->two_factor_confirmed_at) {
            $isExempt = collect(self::EXEMPT_PATH_PATTERNS)->contains(fn ($pattern) => $request->is($pattern) || $request->is("{$pattern}/*"));

            if (!$isExempt) {
                return redirect()->route('two-factor.setup');
            }
        }

        return $next($request);
    }
}
