<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\LINEBaseNotification;
use Illuminate\Support\Facades\Http;

class TwoFactorService
{
    protected User $user;

    protected string $key;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->key = '2fa_code_'.$user->hashed_key;
    }

    public function notify(): void
    {
        if (cache()->has($this->key)) {
            return;
        }
        $code = cache()->remember($this->key, now()->addMinutes(5), fn () => rand(1000, 9999));
        $message = 'กรุณายืนยันตัวตนด้วยการใส่รหัสผ่านสองขั้นตอนดังนี้: '.$code.' หากไม่ใส่รหัสผ่านสองขั้นตอนภายใน 5 นาที รหัสผ่านจะหมดอายุ';
        if (! $this->user->slack_webhook_url) {
            // @TODO: remove LINE notify service
            $this->user->notify(new LINEBaseNotification($message));

            return;
        }
        Http::post($this->user->slack_webhook_url, [
            'text' => $message,
        ]);
    }

    public function verify(int $userCode): bool
    {
        if (! $verifyCode = cache($this->key)) { // expired
            $this->notify();

            return false;
        }

        if ($verifyCode !== $userCode) {
            return false;
        }

        cache()->forget($this->key);

        return true;
    }
}
