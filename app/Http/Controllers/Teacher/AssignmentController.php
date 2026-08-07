<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Requests\Teacher\StoreAssignmentRequest;
use App\Models\Assignment;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;

class AssignmentController extends TeacherController
{
    public function index(Subject $subject)
    {
        $this->authorizeSubjectOwnership($subject);

        return view('teacher.assignments.index', [
            'subject' => $subject,
            'assignments' => $subject->assignments()->withCount('submissions')->latest()->get(),
        ]);
    }

    public function create(Subject $subject)
    {
        $this->authorizeSubjectOwnership($subject);

        return view('teacher.assignments.create', compact('subject'));
    }

    public function store(StoreAssignmentRequest $request, Subject $subject): RedirectResponse
    {
        $subject->assignments()->create($request->validated());

        return redirect()->route('teacher.subjects.assignments.index', $subject)->with('status', 'Assignment created.');
    }

    public function edit(Subject $subject, Assignment $assignment)
    {
        $this->authorizeSubjectOwnership($subject);
        abort_unless($assignment->subject_id === $subject->id, 404);

        return view('teacher.assignments.edit', compact('subject', 'assignment'));
    }

    public function update(StoreAssignmentRequest $request, Subject $subject, Assignment $assignment): RedirectResponse
    {
        abort_unless($assignment->subject_id === $subject->id, 404);

        $assignment->update($request->validated());

        return redirect()->route('teacher.subjects.assignments.index', $subject)->with('status', 'Assignment updated.');
    }

    public function destroy(Subject $subject, Assignment $assignment): RedirectResponse
    {
        $this->authorizeSubjectOwnership($subject);
        abort_unless($assignment->subject_id === $subject->id, 404);

        $assignment->delete();

        return redirect()->route('teacher.subjects.assignments.index', $subject)->with('status', 'Assignment deleted.');
    }
}
