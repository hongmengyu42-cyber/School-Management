<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectWorkspaceController extends TeacherController
{
    /** GET /teacher/subjects — every subject this teacher currently teaches. */
    public function index(Request $request)
    {
        return view('teacher.subjects.index', [
            'subjects' => Subject::with('semester.academicYear')
                ->withCount('students')
                ->where('teacher_id', $request->user()->id)
                ->orderByDesc('id')
                ->paginate(20),
        ]);
    }

    /**
     * GET /teacher/subjects/{subject} — the hub page: roster + quick links
     * into grades/attendance/assignments/quizzes/conduct for this subject.
     */
    public function show(Subject $subject)
    {
        $this->authorizeSubjectOwnership($subject);

        return view('teacher.subjects.show', [
            'subject' => $subject->load('semester.academicYear', 'department'),
            'students' => $subject->students()->with('user')->orderBy('student_number')->get(),
        ]);
    }
}
