<?php

namespace App\Policies;

use App\Models\Grade;
use App\Models\Subject;
use App\Models\User;

class GradePolicy
{
    /** Admins can always view; teachers only for subjects they teach; students only their own. */
    public function view(User $user, Grade $grade): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $grade->subject->teacher_id === $user->id;
        }

        if ($user->isStudent()) {
            return $grade->student->user_id === $user->id;
        }

        if ($user->isParent()) {
            return $user->linkedStudents->contains('id', $grade->student_id);
        }

        return false;
    }

    /**
     * Used by StoreGradeRequest::authorize(). Replaces the legacy pattern of
     * checking $_SESSION['role'] === 'Teacher' AND isSubjectTermLocked()
     * inline inside the form-handling PHP file.
     */
    public function create(User $user, Subject $subject): bool
    {
        if (!$user->isTeacher() || $subject->teacher_id !== $user->id) {
            return false;
        }

        return !$subject->isLocked();
    }

    public function update(User $user, Grade $grade): bool
    {
        if ($user->isAdmin()) {
            return true; // admins can override even locked terms for corrections
        }

        if (!$user->isTeacher() || $grade->subject->teacher_id !== $user->id) {
            return false;
        }

        return !$grade->subject->isLocked();
    }

    public function delete(User $user, Grade $grade): bool
    {
        return $this->update($user, $grade);
    }
}
