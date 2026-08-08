<?php

namespace App\Services;

use App\Models\Grade;
use App\Models\Semester;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Collection;

/**
 * Builds the data behind a student's report card, one semester at a time,
 * and doubles as the shared source of truth for the weighted-average / GPA
 * math (previously only lived inline in Student\GradeController).
 *
 * IMPORTANT: every grade query here is explicitly scoped by student_id.
 * Subject::grades() is a plain hasMany, unfiltered by student — pulling it
 * through $student->subjects()->with('grades') (as the original grades page
 * did) silently mixes in every other enrolled student's grades on any
 * shared subject. buildSubjectReport() queries Grade directly instead.
 */
class ReportCardService
{
    /** Everything needed to render one student's report card for one semester. */
    public function buildForSemester(Student $student, Semester $semester): array
    {
        $subjects = $student->subjects()
            ->where('subjects.semester_id', $semester->id)
            ->with('gradeCategories', 'teacher')
            ->get();

        $subjectReports = $subjects->map(fn (Subject $subject) => $this->buildSubjectReport($subject, $student));

        [$overallAverage, $overallGpa] = $this->summarize($subjectReports);

        return [
            'student' => $student,
            'semester' => $semester,
            'subjects' => $subjectReports,
            'overall_average' => $overallAverage,
            'overall_gpa' => $overallGpa,
            'is_official' => (bool) $semester->is_locked,
        ];
    }

    /**
     * Same shape, but across every subject the student has ever been
     * enrolled in (no semester filter). Used by Student\GradeController's
     * all-time grades page.
     */
    public function buildForAllSubjects(Student $student): array
    {
        $subjects = $student->subjects()->with('gradeCategories', 'teacher')->get();
        $subjectReports = $subjects->map(fn (Subject $subject) => $this->buildSubjectReport($subject, $student));

        [$overallAverage, $overallGpa] = $this->summarize($subjectReports);

        return [
            'subjects' => $subjectReports,
            'overall_average' => $overallAverage,
            'overall_gpa' => $overallGpa,
        ];
    }

    /** One subject's grade breakdown, weighted average, status, and attendance rate for one student. */
    public function buildSubjectReport(Subject $subject, Student $student): array
    {
        $grades = Grade::where('subject_id', $subject->id)
            ->where('student_id', $student->id)
            ->with('category')
            ->get();

        $categories = $subject->gradeCategories;
        $average = $this->weightedAverage($grades, $categories);

        $attendance = $subject->attendance()->where('student_id', $student->id)->get();
        $attendedCount = $attendance->filter->isPresentOrLate()->count();
        $attendanceRate = $attendance->isNotEmpty()
            ? round(($attendedCount / $attendance->count()) * 100, 1)
            : null;

        return [
            'subject' => $subject,
            'grades' => $grades,
            'categories' => $categories,
            'average' => $average,
            'status' => $average !== null
                ? ($average >= Setting::passingThreshold() ? 'Passed' : 'Failed')
                : null,
            'attendance_rate' => $attendanceRate,
            'attendance_days_recorded' => $attendance->count(),
        ];
    }

    /**
     * Weighted by category when categories exist and have grades entered;
     * falls back to a flat average of every grade in the subject otherwise.
     * Categories with zero grades so far are excluded from both the
     * numerator and the weight total (not counted as 0) — an incomplete
     * gradebook mid-term shouldn't read as a failing grade.
     */
    private function weightedAverage(Collection $grades, Collection $categories): ?float
    {
        if ($categories->isNotEmpty()) {
            $weightedTotal = 0;
            $weightUsed = 0;

            foreach ($categories as $category) {
                $categoryGrades = $grades->where('category_id', $category->id);

                if ($categoryGrades->isEmpty()) {
                    continue;
                }

                $weightedTotal += $categoryGrades->avg('grade_value') * ($category->weight_percent / 100);
                $weightUsed += $category->weight_percent;
            }

            if ($weightUsed > 0) {
                return round($weightedTotal / ($weightUsed / 100), 2);
            }
        }

        return $grades->isNotEmpty() ? round($grades->avg('grade_value'), 2) : null;
    }

    /** Standard 100-point → 4.0-scale conversion, matching the existing student grades page. */
    public function percentageToGpa(float $percentage): float
    {
        return match (true) {
            $percentage >= 93 => 4.0,
            $percentage >= 90 => 3.7,
            $percentage >= 87 => 3.3,
            $percentage >= 83 => 3.0,
            $percentage >= 80 => 2.7,
            $percentage >= 77 => 2.3,
            $percentage >= 73 => 2.0,
            $percentage >= 70 => 1.7,
            $percentage >= 67 => 1.3,
            $percentage >= 63 => 1.0,
            $percentage >= 60 => 0.7,
            default => 0.0,
        };
    }

    private function summarize(Collection $subjectReports): array
    {
        $averages = $subjectReports->pluck('average')->filter(fn ($value) => $value !== null);
        $overallAverage = $averages->isNotEmpty() ? round($averages->avg(), 2) : null;
        $overallGpa = $overallAverage !== null ? $this->percentageToGpa($overallAverage) : null;

        return [$overallAverage, $overallGpa];
    }
}
