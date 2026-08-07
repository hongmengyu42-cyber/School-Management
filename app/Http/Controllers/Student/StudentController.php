<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

abstract class StudentController extends Controller
{
    /** Resolves the Student row for the logged-in user. */
    protected function currentStudent(Request $request): Student
    {
        $student = $request->user()->student()->first();

        if ($student) {
            return $student;
        }

        $user = $request->user();

        return Student::create([
            'user_id' => $user->id,
            'student_number' => 'STU-' . str_pad((string) $user->id, 5, '0'),
            'department_id' => $user->department_id,
        ]);
    }

    /**
     * Aborts 403 unless the logged-in student is actually enrolled in this
     * subject — reuses SubjectPolicy::view, which already encodes exactly
     * that check for the Student role.
     */
    protected function authorizeEnrollment(Subject $subject): void
    {
        Gate::authorize('view', $subject);
    }
}
