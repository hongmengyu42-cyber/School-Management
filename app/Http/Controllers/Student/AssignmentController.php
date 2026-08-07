<?php

namespace App\Http\Controllers\Student;

use App\Http\Requests\Student\SubmitAssignmentRequest;
use App\Models\Assignment;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends StudentController
{
    public function index(Request $request, Subject $subject)
    {
        $this->authorizeEnrollment($subject);
        $student = $this->currentStudent($request);

        $assignments = $subject->assignments()->with([
            'submissions' => fn ($q) => $q->where('student_id', $student->id),
        ])->latest()->get();

        return view('student.assignments.index', compact('subject', 'assignments'));
    }

    public function show(Request $request, Subject $subject, Assignment $assignment)
    {
        $this->authorizeEnrollment($subject);
        abort_unless($assignment->subject_id === $subject->id, 404);

        $student = $this->currentStudent($request);
        $submission = $assignment->submissions()->where('student_id', $student->id)->first();

        return view('student.assignments.show', compact('subject', 'assignment', 'submission'));
    }

    /**
     * Handles both the first submission and re-submission (before the due
     * date — the legacy system allowed re-uploads up until the deadline).
     */
    public function store(SubmitAssignmentRequest $request, Subject $subject, Assignment $assignment): RedirectResponse
    {
        $this->authorizeEnrollment($subject);
        abort_unless($assignment->subject_id === $subject->id, 404);

        $student = $this->currentStudent($request);

        $path = $request->file('file')->store("submissions/{$assignment->id}", 'public');

        $assignment->submissions()->updateOrCreate(
            ['student_id' => $student->id],
            ['file_path' => $path, 'submitted_at' => now()]
        );

        return redirect()
            ->route('student.subjects.assignments.show', [$subject, $assignment])
            ->with('status', 'Assignment submitted.');
    }
}
