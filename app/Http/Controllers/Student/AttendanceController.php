<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;

class AttendanceController extends StudentController
{
    public function index(Request $request)
    {
        $student = $this->currentStudent($request);
        $subjects = $student->subjects()->with(['attendance' => fn ($q) => $q->orderByDesc('date')])->get();

        $summaries = $subjects->map(function ($subject) {
            $records = $subject->attendance;
            $total = $records->count();
            $presentOrLate = $records->filter->isPresentOrLate()->count();

            return [
                'subject' => $subject,
                'records' => $records,
                'percentage' => $total > 0 ? round(($presentOrLate / $total) * 100, 1) : null,
            ];
        });

        return view('student.attendance.index', compact('summaries'));
    }
}
