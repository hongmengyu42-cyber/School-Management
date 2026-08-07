<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActivityLog;

class ActivityLogController extends AdminController
{
    public function __invoke()
    {
        return view('admin.activity-logs.index', [
            'logs' => ActivityLog::with('user')->latest()->paginate(50),
        ]);
    }
}
