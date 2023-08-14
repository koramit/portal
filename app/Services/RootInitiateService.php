<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Notifications\LINEBaseNotification;

class RootInitiateService
{
    public function isRootInitiated(): bool
    {
        return (bool) cache()->rememberForever('root-initiated', function () {
            return Role::query()
                ->withCount('users')
                ->where('name', 'root')
                ->first()?->users_count > 0;
        });
    }

    public function sendCode(User $user): void
    {
        $code = rand(100000, 999999);
        cache()->put('root-initiate-code', $code, now()->addMinutes(5));

        $user->notify(new LINEBaseNotification("ใช้ code ต่อไปนี้เพื่อเป็น root: $code"));
    }

    public function verifyCode(int $code): bool
    {
        if (cache('root-initiate-code') !== $code) {
            return false;
        }

        cache()->forget('root-initiate-code');
        cache()->forever('root-initiated', true);

        return true;
    }
}
