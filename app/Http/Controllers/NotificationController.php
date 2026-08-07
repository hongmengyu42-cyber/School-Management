<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return view('notifications.index', [
            'notifications' => $request->user()->notifications()->paginate(30),
        ]);
    }

    /** Marks one notification read and redirects into whatever it points at. */
    public function read(Request $request, string $notificationId): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        if (isset($notification->data['thread_id'])) {
            $routeName = $request->user()->isTeacher() ? 'teacher.inbox.show' : 'student.messages.show';
            return redirect()->route($routeName, $notification->data['thread_id']);
        }

        if (isset($notification->data['subject_id']) && $request->user()->isStudent()) {
            return redirect()->route('student.subjects.show', $notification->data['subject_id']);
        }

        return redirect()->route('notifications.index');
    }

    public function readAll(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}
