<?php

namespace App\Policies;

use App\Models\ServiceRequestForm;
use App\Models\User;

class ServiceRequestFormPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function response(User $user, ServiceRequestForm $form): bool
    {
        return $form->status === 'pending' && $user->can('approve_request_form');
    }

    public function cancel(User $user, ServiceRequestForm $form): bool
    {
        return $form->status === 'pending' && $form->requester_id === $user->id;
    }

    public function revoke(User $user, ServiceRequestForm $form): bool
    {
        return $form->status === 'approved' && $user->can('approve_request_form');
    }
}
