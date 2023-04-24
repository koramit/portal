<?php

namespace App\Notifications;

use App\Models\User;
use App\Notifications\Messages\LINENotifyMessage;
use Illuminate\Notifications\Notification;

class LINEBaseNotification extends Notification
{
    public function __construct(
        protected string $message
    ) {
    }

    public function via(object $notifiable): ?string
    {
        if (! $notifiable instanceof User) {
            return null;
        }

        return LINEChannel::class;
    }

    public function toLINE(object $notifiable): LINENotifyMessage
    {
        return (new LINENotifyMessage())->text($this->message);
    }
}
