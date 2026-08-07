<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;

class GradeController extends StudentController
{
    /**
     * Replaces legacy grading_helpers.php's per-subject weighted-average
     * computation, with graceful fallback to a simple average when a
     * subject has no weighted categories defined.
     */
    public function index(Request $request)
    {
        $student = $this->currentStudent($request);
        $subjects = $student->subjects()->with(['grades' => fn ($q) => $q->with('category'), 'gradeCategories'])->get();

        $subjectSummaries = $subjects->map(function ($subject) {
            $grades = $subject->grades;
            $categories = $subject->gradeCategories;

            if ($categories->isNotEmpty()) {
                $weightedTotal = 0;
                $weightUsed = 0;

                foreach ($categories as $category) {
                    $categoryGrades = $grades->where('category_id', $category->id);
                    if ($categoryGrades->isEmpty()) {
                        continue;
                    }
                    $categoryAverage = $categoryGrades->avg('grade_value');
                    $weightedTotal += $categoryAverage * ($category->weight_percent / 100);
                    $weightUsed += $category->weight_percent;
                }

                // Fallback to simple average if no categorized grades were entered yet.
                $average = $weightUsed > 0 ? round($weightedTotal / ($weightUsed / 100), 2) : null;
            }

            if (empty($average) && $grades->isNotEmpty()) {
                $average = round($grades->avg('grade_value'), 2);
            }

            return [
                'subject' => $subject,
                'average' => $average ?? null,
                'grades' => $grades,
            ];
        });

        $overallAverage = $subjectSummaries->pluck('average')->filter()->avg();

        return view('student.grades.index', [
            'subjectSummaries' => $subjectSummaries,
            'overallAverage' => $overallAverage ? round($overallAverage, 2) : null,
            'overallGpa' => $overallAverage ? $this->percentageToGpa($overallAverage) : null,
        ]);
    }

    /** Standard 100-point → 4.0-scale conversion, matching the legacy report card. */
    private function percentageToGpa(float $percentage): float
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
}
