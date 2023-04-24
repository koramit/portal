<?php

namespace App\Policies;

use App\Models\PersonalAccessToken;
use App\Models\User;

class PersonalAccessTokenPolicy
{
    /**
     * Create a new policy instance.
     */
    public function destroy(User $user, PersonalAccessToken $token): bool
    {
        return $token->status === 'active'
            && ($user->id === $token->tokenable_id || $user->can('revoke_any_tokens'));
    }
}
