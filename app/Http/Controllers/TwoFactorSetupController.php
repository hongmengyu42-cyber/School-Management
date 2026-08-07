<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TwoFactorSetupController extends Controller
{
    public function show(Request $request)
    {
        return view('auth.two-factor-setup', [
            'alreadyEnabled' => (bool) $request->user()->two_factor_secret,
        ]);
    }
}
