<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Concerns\GeneratesReportCards;
use App\Models\Semester;
use App\Models\Student;
use Illuminate\Http\Request;

class ReportCardController extends ParentController
{
    use GeneratesReportCards;

    /** GET /parent/children/{student}/report-card */
    public function index(Request $request, Student $student)
    {
        $this->authorizeLinkedStudent($request, $student);

        return view('parent.report-card.index', [
            'student' => $student->load('user'),
            'semesters' => $this->semestersForStudent($student),
        ]);
    }

    /** GET /parent/children/{student}/report-card/{semester} */
    public function show(Request $request, Student $student, Semester $semester)
    {
        $this->authorizeLinkedStudent($request, $student);

        return $this->streamReportCardPdf($student, $semester);
    }
}
