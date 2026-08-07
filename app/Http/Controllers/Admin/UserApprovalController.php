<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Replaces the "Approve" button on the legacy manage_users.php page.
 * Separate single-action controller since it's a distinct, narrow
 * operation (not a full resource update) — invoked via POST /admin/approve-users/{user}.
 */
class UserApprovalController extends AdminController
{
    public function __invoke(Request $request, User $user): RedirectResponse
    {
        $user->update(['status' => 'Active']);

        $this->logActivity($request, 'user.approved', "Approved user {$user->username}");

        return back()->with('status', "{$user->username} approved.");
    }
}
