<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class UserApproved extends Notification
{
    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Account approved',
            'body' => 'Your account has been approved. You now have full access.',
        ];
    }
}
