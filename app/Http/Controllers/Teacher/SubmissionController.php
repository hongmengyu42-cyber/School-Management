<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Requests\Teacher\GradeSubmissionRequest;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class SubmissionController extends TeacherController
{
    /** GET /teacher/assignments/{assignment}/submissions */
    public function index(Assignment $assignment)
    {
        Gate::authorize('update', $assignment->subject);

        return view('teacher.submissions.index', [
            'assignment' => $assignment,
            'submissions' => $assignment->submissions()->with('student.user')->get(),
        ]);
    }

    /** PUT /teacher/submissions/{submission} — score + feedback. */
    public function update(GradeSubmissionRequest $request, AssignmentSubmission $submission): RedirectResponse
    {
        $submission->update($request->validated());

        return redirect()
            ->route('teacher.assignments.submissions.index', $submission->assignment)
            ->with('status', 'Submission graded.');
    }
}
