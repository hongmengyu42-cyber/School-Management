<?php

namespace App\Policies;

use App\Models\DisputeThread;
use App\Models\User;

class DisputeThreadPolicy
{
    /** Students see only their own threads; teachers only threads on subjects they teach; admins see all. */
    public function view(User $user, DisputeThread $thread): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isStudent()) {
            return $thread->student->user_id === $user->id;
        }

        if ($user->isTeacher()) {
            return $thread->subject->teacher_id === $user->id;
        }

        return false;
    }

    public function reply(User $user, DisputeThread $thread): bool
    {
        return $this->view($user, $thread) && $thread->status === 'Open';
    }
}
