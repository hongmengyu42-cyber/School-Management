<?php

namespace App\Http\Controllers\Student;

use App\Models\DisputeThread;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MessageController extends StudentController
{
    public function index(Request $request)
    {
        $student = $this->currentStudent($request);

        $threads = $student->disputeThreads()
            ->with(['subject', 'messages' => fn ($q) => $q->latest()->limit(1)])
            ->latest('updated_at')
            ->get();

        return view('student.messages.index', compact('threads'));
    }

    /** "Message Teacher" button on a subject page — one click, no form. */
    public function startForSubject(Request $request, Subject $subject): RedirectResponse
    {
        $this->authorizeEnrollment($subject);
        $student = $this->currentStudent($request);

        $thread = DisputeThread::findOrCreateFor($student->id, $subject->id);

        return redirect()->route('student.messages.show', $thread);
    }

    public function show(DisputeThread $thread)
    {
        Gate::authorize('view', $thread);

        $thread->messages()->where('sender_id', '!=', request()->user()->id)->update(['is_read' => true]);

        return view('student.messages.show', [
            'thread' => $thread->load('subject.teacher', 'messages.sender'),
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

        if ($thread->subject->teacher) {
            $thread->subject->teacher->notify(new \App\Notifications\NewThreadMessage($message));
        }

        return redirect()->route('student.messages.show', $thread)->with('status', 'Message sent.');
    }
}
