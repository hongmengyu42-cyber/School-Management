<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AssignTeacherRequest;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;

class TeacherAssignmentController extends AdminController
{
    public function __invoke(AssignTeacherRequest $request): RedirectResponse
    {
        $subject = Subject::findOrFail($request->subject_id);
        $subject->update(['teacher_id' => $request->teacher_id]);

        $this->logActivity(
            $request,
            'subject.teacher_assigned',
            "Assigned teacher #{$request->teacher_id} to subject {$subject->subject_name}"
        );

        return back()->with('status', 'Teacher assigned.');
    }
}
