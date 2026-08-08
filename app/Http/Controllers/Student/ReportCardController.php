<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Concerns\GeneratesReportCards;
use App\Models\Semester;
use Illuminate\Http\Request;

class ReportCardController extends StudentController
{
    use GeneratesReportCards;

    /** GET /student/report-card — list semesters with a report card available. */
    public function index(Request $request)
    {
        $student = $this->currentStudent($request);

        return view('student.report-card.index', [
            'semesters' => $this->semestersForStudent($student),
        ]);
    }

    /** GET /student/report-card/{semester} — stream own report card PDF. */
    public function show(Request $request, Semester $semester)
    {
        $student = $this->currentStudent($request);

        return $this->streamReportCardPdf($student, $semester);
    }
}
