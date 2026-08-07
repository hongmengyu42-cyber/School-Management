<?php

namespace App\Http\Controllers\Parent;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends ParentController
{
    public function show(Request $request, Student $student)
    {
        $this->authorizeLinkedStudent($request, $student);

        $student->load('user', 'department');

        $subjects = $student->subjects()->with('teacher')->get();

        $grades = \App\Models\Grade::where('student_id', $student->id)
            ->with('subject', 'category')
            ->latest()
            ->get()
            ->groupBy('subject_id');

        $attendanceBySubject = $subjects->mapWithKeys(function ($subject) use ($student) {
            $records = $subject->attendance()->where('student_id', $student->id)->get();
            $presentOrLate = $records->filter->isPresentOrLate()->count();

            return [$subject->id => [
                'total' => $records->count(),
                'percentage' => $records->isNotEmpty() ? round(($presentOrLate / $records->count()) * 100, 1) : null,
            ]];
        });

        $conductRecords = \App\Models\ConductRecord::where('student_id', $student->id)
            ->with('recordedBy')
            ->latest('incident_date')
            ->get();

        $extracurricular = \App\Models\ExtracurricularActivity::where('student_id', $student->id)
            ->latest('date_recorded')
            ->get();

        $invoices = \App\Models\Invoice::where('student_id', $student->id)
            ->latest('due_date')
            ->get();

        return view('parent.students.show', compact(
            'student', 'subjects', 'grades', 'attendanceBySubject',
            'conductRecords', 'extracurricular', 'invoices'
        ));
    }
}
