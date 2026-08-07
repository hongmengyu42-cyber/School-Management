<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Subject;
use Illuminate\Http\Request;

class TimetableController extends TeacherController
{
    public function index(Request $request)
    {
        return view('teacher.timetable.index', [
            'subjects' => Subject::where('teacher_id', $request->user()->id)
                ->with('semester')
                ->orderBy('subject_name')
                ->get(),
        ]);
    }
}
