<?php

namespace App\Notifications;

use App\Models\Student;
use App\Models\Subject;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


// use App\Models\Setting;
/**
 * Fired once a student reaches Setting::consecutiveAbsenceAlertThreshold()
 * consecutive "Absent" marks in a single subject. Sent to the student and
 * every linked parent (see Student::notifiableUsers()) so the absence
 * pattern gets caught before it becomes a semester-ending problem.
 */
class AttendanceAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Student $student,
        private Subject $subject,
        private int $consecutiveAbsences,
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Attendance alert: {$this->subject->subject_name}")
            ->greeting("Hello {$notifiable->full_name},")
            ->line($this->summary())
            ->line('Please reach out to the teacher or the school office if there is something going on that we should know about.')
            ->action('View attendance', url('/'))
            ->line('You are receiving this because of the school\'s attendance alert policy.');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Attendance alert',
            'body' => $this->summary(),
            'subject_id' => $this->subject->id,
            'is_alert' => true,
        ];
    }

    private function summary(): string
    {
        return "{$this->student->user->full_name} has been marked absent "
            . "{$this->consecutiveAbsences} classes in a row for {$this->subject->subject_name}.";
    }
}
