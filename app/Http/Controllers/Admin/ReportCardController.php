<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\GeneratesReportCards;
use App\Models\Semester;
use App\Models\Student;

class ReportCardController extends AdminController
{
    use GeneratesReportCards;

    /** GET /admin/students/{student}/report-card */
    public function index(Student $student)
    {
        return view('admin.report-card.index', [
            'student' => $student->load('user'),
            'semesters' => $this->semestersForStudent($student),
        ]);
    }

    /** GET /admin/students/{student}/report-card/{semester} */
    public function show(Student $student, Semester $semester)
    {
        return $this->streamReportCardPdf($student, $semester);
    }
}
