<?php

namespace App\Http\Controllers\Teacher;


use App\Models\DisputeThread;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class InboxController extends TeacherController
{
    /** GET /teacher/inbox — threads across every subject this teacher teaches. */
    public function index(Request $request)
    {
        $threads = DisputeThread::whereHas('subject', fn ($q) => $q->where('teacher_id', $request->user()->id))
            ->with(['student.user', 'subject', 'messages' => fn ($q) => $q->latest()->limit(1)])
            ->latest('updated_at')
            ->get();

        return view('teacher.inbox.index', compact('threads'));
    }

    public function show(DisputeThread $thread)
    {
        Gate::authorize('view', $thread);

        $thread->messages()->where('sender_id', '!=', request()->user()->id)->update(['is_read' => true]);

        return view('teacher.inbox.show', [
            'thread' => $thread->load('student.user', 'subject', 'messages.sender'),
        ]);
    }

    public function reply(Request $request, DisputeThread $thread): RedirectResponse
    {
        Gate::authorize('reply', $thread);

        $validated = $request->validate(['message' => ['required', 'string']]);

        $message = $thread->messages()->create([
            'sender_id' => $request->user()->id,
            'message' => $validated['message'],
        ]);
        $thread->touch();

        $thread->student->user->notify(new \App\Notifications\NewThreadMessage($message));

        return redirect()->route('teacher.inbox.show', $thread)->with('status', 'Message sent.');
    }
}
