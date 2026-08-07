<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Support\Facades\Gate;

abstract class TeacherController extends Controller
{
    /**
     * Every teacher-facing subject-scoped controller starts here. Aborts
     * 403 unless the logged-in teacher actually owns this subject —
     * replaces the legacy pattern of checking $_SESSION['user_id'] ==
     * $subject['teacher_id'] inline at the top of each file.
     */
    protected function authorizeSubjectOwnership(Subject $subject): void
    {
        Gate::authorize('update', $subject);
    }

    /**
     * For write actions on grades/attendance specifically, where a locked
     * term must also block the write (SubjectPolicy::manageGrades already
     * encodes "owns it AND not locked").
     */
    protected function authorizeSubjectMutable(Subject $subject): void
    {
        Gate::authorize('manageGrades', $subject);
    }
}
