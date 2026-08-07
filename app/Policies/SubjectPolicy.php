<?php

namespace App\Policies;

use App\Models\Subject;
use App\Models\User;

class SubjectPolicy
{
    public function view(User $user, Subject $subject): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $subject->teacher_id === $user->id;
        }

        if ($user->isStudent()) {
            return $subject->students->contains('user_id', $user->id);
        }

        if ($user->isParent()) {
            $studentIds = $user->linkedStudents->pluck('id');

            return $subject->students->pluck('id')->intersect($studentIds)->isNotEmpty();
        }

        return false;
    }

    public function update(User $user, Subject $subject): bool
    {
        return $user->isAdmin() || ($user->isTeacher() && $subject->teacher_id === $user->id);
    }

    public function manageGrades(User $user, Subject $subject): bool
    {
        return $this->update($user, $subject) && !$subject->isLocked() || $user->isAdmin();
    }

    public function delete(User $user, Subject $subject): bool
    {
        return $user->isAdmin();
    }
}
