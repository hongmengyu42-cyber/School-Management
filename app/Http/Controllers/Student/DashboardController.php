<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;

class DashboardController extends StudentController
{
    public function __invoke(Request $request)
    {
        $student = $this->currentStudent($request);

        $subjects = $student->subjects()->with('teacher', 'semester')->get();

        $upcomingAssignments = \App\Models\Assignment::whereIn('subject_id', $subjects->pluck('id'))
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        $unreadThreadCount = $student->disputeThreads()
            ->whereHas('messages', fn ($q) => $q->where('sender_id', '!=', $request->user()->id)->where('is_read', false))
            ->count();

        return view('student.dashboard', compact('student', 'subjects', 'upcomingAssignments', 'unreadThreadCount'));
    }
}
