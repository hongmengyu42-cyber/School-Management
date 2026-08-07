<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\Subject;
use App\Models\User;

class AttendancePolicy
{
    public function view(User $user, Attendance $attendance): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $attendance->subject->teacher_id === $user->id;
        }

        if ($user->isStudent()) {
            return $attendance->student->user_id === $user->id;
        }

        if ($user->isParent()) {
            return $user->linkedStudents->contains('id', $attendance->student_id);
        }

        return false;
    }

    public function create(User $user, Subject $subject): bool
    {
        if (!$user->isTeacher() || $subject->teacher_id !== $user->id) {
            return false;
        }

        return !$subject->isLocked();
    }

    public function update(User $user, Attendance $attendance): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (!$user->isTeacher() || $attendance->subject->teacher_id !== $user->id) {
            return false;
        }

        return !$attendance->subject->isLocked();
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        return $this->update($user, $attendance);
    }
}
