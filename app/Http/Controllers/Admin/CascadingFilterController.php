<?php

namespace App\Http\Controllers\Admin;

use App\Models\Semester;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Replaces hierarchy_helpers.php's AJAX endpoints. A single controller with
 * one action per link in the chain keeps this consistent with how the
 * legacy cascading-filters.js already calls out to the backend.
 */
class CascadingFilterController extends AdminController
{
    public function semestersForYear(Request $request): JsonResponse
    {
        $request->validate(['academic_year_id' => 'required|exists:academic_years,id']);

        return response()->json(
            Semester::where('academic_year_id', $request->academic_year_id)
                ->orderByDesc('id')
                ->get(['id', 'name', 'is_locked'])
        );
    }

    public function subjectsForSemester(Request $request): JsonResponse
    {
        $request->validate(['semester_id' => 'required|exists:semesters,id']);

        return response()->json(
            Subject::where('semester_id', $request->semester_id)
                ->orderBy('subject_name')
                ->get(['id', 'subject_name', 'subject_code', 'department_id'])
        );
    }
}
