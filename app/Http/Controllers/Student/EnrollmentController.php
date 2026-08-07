<?php

namespace App\Http\Controllers\Student;

use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EnrollmentController extends StudentController
{
    public function create()
    {
        return view('student.enrollments.create');
    }

    /** Mirrors legacy self-enroll: student types the access code the teacher/admin shared. */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(['access_code' => ['required', 'string']]);

        $subject = Subject::findByAccessCode($validated['access_code']);

        if (!$subject) {
            return back()->withErrors(['access_code' => 'No subject matches that access code.']);
        }

        $student = $this->currentStudent($request);

        if ($student->subjects()->where('subjects.id', $subject->id)->exists()) {
            return back()->withErrors(['access_code' => "You're already enrolled in {$subject->subject_name}."]);
        }

        $student->enrollments()->create(['subject_id' => $subject->id]);

        return redirect()->route('student.subjects.index')->with('status', "Enrolled in {$subject->subject_name}.");
    }
}
