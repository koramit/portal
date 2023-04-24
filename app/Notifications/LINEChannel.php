<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class LINEChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! $notifiable->line_notify_enabled) {
            return;
        }

        /** @var LINEBaseNotification $notification */
        $notify = $notification->toLINE($notifiable);

        Http::asForm()
            ->withToken($notifiable->line_notify_token)
            ->post('https://notify-api.line.me/api/notify', $notify->getMessages());
    }
}
