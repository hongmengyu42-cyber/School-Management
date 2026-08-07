<?php

namespace App\Http\Controllers\Parent;

use Illuminate\Http\Request;

class DashboardController extends ParentController
{
    public function __invoke(Request $request)
    {
        $children = $request->user()->linkedStudents()->with('user', 'subjects')->get();

        $summaries = $children->map(function ($student) {
            $grades = \App\Models\Grade::where('student_id', $student->id)->get();
            $attendance = \App\Models\Attendance::where('student_id', $student->id)->get();
            $presentOrLate = $attendance->filter->isPresentOrLate()->count();

            return [
                'student' => $student,
                'subjectCount' => $student->subjects->count(),
                'averageGrade' => $grades->isNotEmpty() ? round($grades->avg('grade_value'), 1) : null,
                'attendancePercentage' => $attendance->isNotEmpty()
                    ? round(($presentOrLate / $attendance->count()) * 100, 1)
                    : null,
            ];
        });

        return view('parent.dashboard', compact('summaries'));
    }
}
