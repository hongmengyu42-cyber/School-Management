<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Student;
use App\Models\Subject;
use App\Notifications\AttendanceAlert;
// use Illuminate\Notifications\Notification;

use Illuminate\Support\Facades\Notification;
/**
 * Called after attendance is saved for a subject/day. Looks at the most
 * recently recorded attendance rows (not calendar days — only days a
 * teacher actually took attendance count) for one student in one subject,
 * and fires AttendanceAlert the moment the consecutive-absence streak first
 * reaches the configured threshold.
 *
 * "First reaches" (===, not >=) so a student who's been absent for weeks
 * doesn't re-trigger an alert on every single subsequent absence.
 */
class AttendanceAlertService
{
    public function checkStudent(Subject $subject, Student $student): void
    {
        $threshold = Setting::consecutiveAbsenceAlertThreshold();

        // Pull one extra record past the threshold so we can tell "streak
        // just reached the threshold today" apart from "streak has already
        // been past the threshold for a while" — otherwise this would
        // re-fire on every single absence after the first alert.
        $recentStatuses = $subject->attendance()
            ->where('student_id', $student->id)
            ->orderByDesc('date')
            ->limit($threshold + 1)
            ->pluck('status');

        if ($recentStatuses->count() < $threshold) {
            return;
        }

        $streakWindow = $recentStatuses->take($threshold);

        if ($streakWindow->contains(fn (string $status) => $status !== 'Absent')) {
            return;
        }

        $dayBeforeStreak = $recentStatuses->get($threshold);

        if ($dayBeforeStreak === 'Absent') {
            return; // streak already exceeded the threshold before today; already alerted
        }

        Notification::send(
            $student->notifiableUsers(),
            new AttendanceAlert($student, $subject, $threshold)
        );
    }
}
