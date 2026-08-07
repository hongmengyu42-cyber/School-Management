<?php

namespace App\Http\Controllers\Student;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends StudentController
{
    public function index(Request $request)
    {
        $student = $this->currentStudent($request);

        return view('student.subjects.index', [
            'subjects' => $student->subjects()->with('teacher', 'semester.academicYear')->paginate(20),
        ]);
    }

    public function show(Request $request, Subject $subject)
    {
        $this->authorizeEnrollment($subject);
        $student = $this->currentStudent($request);

        return view('student.subjects.show', [
            'subject' => $subject->load('teacher', 'semester.academicYear'),
            'grades' => $subject->grades()->where('student_id', $student->id)->with('category')->get(),
            'attendance' => $subject->attendance()->where('student_id', $student->id)->orderByDesc('date')->limit(30)->get(),
        ]);
    }
}
