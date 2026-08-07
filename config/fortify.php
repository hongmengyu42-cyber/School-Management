<?php

// This is not the full stock config/fortify.php — after running
// `php artisan fortify:install`, only change these specific keys to match
// what our FortifyServiceProvider and views expect. Everything else in the
// generated file can stay at its default.

return [

    // Legacy system logs in with a username, not an email address.
    'username' => 'username',

    // Fortify still needs an "email" field internally for password resets;
    // that's unaffected by the line above and needs no change.

    'guard' => 'web',

    'passwords' => 'users',

    'home' => '/dashboard',

    'prefix' => '',

    'domain' => null,

    'middleware' => ['web'],

    'limiters' => [
        'login' => 'login',
        'two-factor' => 'two-factor',
    ],

    'views' => true,

    'features' => [
        \Laravel\Fortify\Features::registration(),
        \Laravel\Fortify\Features::resetPasswords(),
        \Laravel\Fortify\Features::updateProfileInformation(),
        \Laravel\Fortify\Features::updatePasswords(),
        \Laravel\Fortify\Features::twoFactorAuthentication([
            // Confirm password is required to disable 2FA, matching the
            // legacy system's "2FA is mandatory for Admin/Teacher" policy —
            // we enforce the *mandatory* part separately, via
            // EnsureTwoFactorIsConfigured middleware (Step 3.3), since
            // Fortify itself treats 2FA as opt-in.
            'confirm' => true,
            // confirmPassword is intentionally false: Fortify's default is
            // true, which requires a password-re-confirmation view before
            // any 2FA management action — a view this app doesn't have.
            // Turning it on requires supplying Fortify::confirmPasswordView()
            // and handling the confirm-password redirect inside the fetch()
            // flow in two-factor-setup.blade.php, neither of which exist yet.
            // For a production deployment this should be revisited.
            'confirmPassword' => false,
        ]),
    ],

];
