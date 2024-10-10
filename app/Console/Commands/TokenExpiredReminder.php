<?php

namespace App\Console\Commands;

use App\Models\PersonalAccessToken;
use App\Models\User;
use App\Notifications\LINEBaseNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TokenExpiredReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'token:expired-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        PersonalAccessToken::query()
            ->with('tokenable')
            ->where('status', 1) // active
            ->where('expires_at', '<', now()->addDays(5)) // start reminder 5 days before expiration
            ->each(function (PersonalAccessToken $token) {
                if ($token->status !== 'active') {
                    return;
                }
                $message = "Your token $token->name will be expired in {$token->expires_at->diffForHumans()}.";
                // @TODO: remove LINE notify service
                /** @var User $user */
                $user = $token->tokenable;
                if (! $user->slack_webhook_url) {
                    $notification = new LINEBaseNotification($message);
                    $user->notify($notification);

                    return;
                }
                Http::post($user->slack_webhook_url, [
                    'text' => $message,
                ]);
            });
    }
}
