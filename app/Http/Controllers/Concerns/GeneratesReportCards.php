<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Semester;
use App\Models\Student;
use App\Services\ReportCardService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

trait GeneratesReportCards
{
    protected function streamReportCardPdf(Student $student, Semester $semester): Response
    {
        $report = app(ReportCardService::class)->buildForSemester($student, $semester);

        $pdf = Pdf::loadView('reports.report-card-pdf', $report)->setPaper('a4');

        $filename = Str::slug("report-card-{$student->student_number}-{$semester->name}") . '.pdf';

        return $pdf->stream($filename);
    }

    /** Semesters this student has at least one subject in, most recent first. */
    protected function semestersForStudent(Student $student): Collection
    {
        $semesterIds = $student->subjects()->pluck('subjects.semester_id')->filter()->unique();

        return Semester::whereIn('id', $semesterIds)
            ->with('academicYear')
            ->orderByDesc('start_date')
            ->get();
    }
}
