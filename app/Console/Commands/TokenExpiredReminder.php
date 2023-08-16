<?php

namespace App\Console\Commands;

use App\Models\PersonalAccessToken;
use App\Notifications\LINEBaseNotification;
use Illuminate\Console\Command;

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
                $notification = new LINEBaseNotification("Your token $token->name will be expired in {$token->expires_at->diffForHumans()}.");
                $token->tokenable->notify($notification);
            });
    }
}
