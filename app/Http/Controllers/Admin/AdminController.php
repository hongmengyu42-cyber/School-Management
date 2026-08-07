<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

abstract class AdminController extends Controller
{
    /**
     * Replaces the ad-hoc `logAction($pdo, ...)` calls sprinkled through the
     * legacy admin scripts. Call this after any create/update/delete so the
     * Activity Logs screen has something to show.
     */
    protected function logActivity(Request $request, string $action, ?string $description = null): void
    {
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => $action,
            'description' => $description,
            'ip_address' => $request->ip(),
        ]);
    }
}
