<?php

namespace App\Notifications;

use App\Models\DisputeMessage;
use Illuminate\Notifications\Notification;

class NewThreadMessage extends Notification
{
    public function __construct(private DisputeMessage $message)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $thread = $this->message->thread;

        return [
            'title' => 'New message',
            'body' => $this->message->sender->full_name . ' sent a message about ' . $thread->subject->subject_name,
            'thread_id' => $thread->id,
            'sender_role' => $this->message->sender->role,
        ];
    }
}
