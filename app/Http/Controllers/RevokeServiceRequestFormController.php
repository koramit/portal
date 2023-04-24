<?php

namespace App\Http\Controllers;


use App\Models\Ability;
use App\Models\Role;
use App\Models\ServiceRequestForm;
use App\Notifications\LINEBaseNotification;
use App\Services\RoleUserService;
use Illuminate\Http\Request;

class RevokeServiceRequestFormController extends Controller
{
    public function __invoke(ServiceRequestForm $form, Request $request)
    {
        $requester = $form->requester;
        $approvedServices = $form->form
            ->filter(fn ($value, $key) => $value === true)
            ->keys();
        $rolesToRevokes = Role::query()
            ->whereIn('name', $approvedServices)
            ->pluck('id');
        $requester->roles()->detach($rolesToRevokes);
        $requester->flushPrivileges();

        $abilitiesToRevokes = Ability::query()
            ->whereHas('roles', fn ($query) => $query->whereIn('id', $rolesToRevokes))
            ->pluck('name');
        $tokenAbilitiesToRevokes = collect(RoleUserService::TOKEN_ABILITIES)
            ->filter(fn ($ability) => $abilitiesToRevokes->contains($ability['can']))
            ->pluck('ability');

        $authority = $request->user();
        $form->status = 'revoked';
        $form->revoke_authority_id = $authority->id;
        $form->save();
        $requester->tokens()
            ->active()
            ->each(function ($token) use ($tokenAbilitiesToRevokes, $authority, $requester) {
                $match = 0;
                $tokenAbilitiesToRevokes->each(function ($ability) use ($token, &$match) {
                    if ($token->can($ability)) {
                        $match++;
                    }
                });

                if ($match === 0) {
                    return;
                }

                $token->revoker_id = $authority->id;
                $token->status = 'revoked';
                $token->save();

                $notifyText = "Your token {$token->name} has been revoked by {$authority->name} because your service request has been revoked.";
                $requester->notify(new LINEBaseNotification($notifyText));
            });

        return back()->with('status', 'Approved Service request has been revoked.');
    }
}
