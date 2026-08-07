<?php

namespace App\Notifications;

use App\Models\Grade;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GradePosted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private Grade $grade)
    {
    }

    /**
     * Always logged in-app; also emailed so parents (who may not log in
     * regularly) actually see it. Failing grades go through the same
     * channels — only the wording changes — so bad news is never silently
     * downgraded to database-only.
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->isFailing()
                ? "Grade alert: {$this->grade->subject->subject_name}"
                : "New grade posted: {$this->grade->subject->subject_name}")
            ->greeting("Hello {$notifiable->full_name},")
            ->line($this->summary());

        if ($this->isFailing()) {
            $mail->line('This grade is below the school\'s passing threshold of '
                . rtrim(rtrim(number_format(Setting::passingThreshold(), 1), '0'), '.') . '%.');
        }

        return $mail->line("Subject: {$this->grade->subject->subject_name}")
            ->line('Student: ' . $this->grade->student->user->full_name)
            ->action('View grades', url('/'))
            ->line('You are receiving this because a new grade was recorded.');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => $this->isFailing() ? 'Grade alert' : 'New grade posted',
            'body' => $this->summary(),
            'subject_id' => $this->grade->subject_id,
            'is_alert' => $this->isFailing(),
        ];
    }

    private function summary(): string
    {
        return ($this->grade->label ?? $this->grade->category?->name ?? 'A grade') .
            " was recorded for {$this->grade->student->user->full_name} in " .
            "{$this->grade->subject->subject_name}: {$this->grade->grade_value}";
    }

    private function isFailing(): bool
    {
        return $this->grade->status === 'Failed';
    }
}
