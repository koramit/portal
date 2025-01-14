<?php

namespace App\Http\Controllers;

use App\Models\PersonalAccessToken;
use App\Models\User;
use App\Notifications\LINEBaseNotification;
use App\Services\RoleUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PersonalAccessTokenController extends Controller
{
    public function store(Request $request)
    {
        $service = new RoleUserService;
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            ...$service->getAbilityValidationRules($request->user()),
        ]);

        $tokenAbilities = $service->availableServices($user)
            ->filter(fn ($ability) => in_array($ability['name'], array_keys($validated)))
            ->pluck('ability')
            ->toArray();

        $plainTextToken = $user->createToken(
            $validated['name'],
            $tokenAbilities,
            now()->addDays(config('sanctum.expiration_days')),
        )->plainTextToken;

        return back()->with([
            'status' => 'Token created successfully : You cannot see the token again, so please copy it now.',
            'token' => $plainTextToken,
        ]);
    }

    public function destroy(PersonalAccessToken $token, Request $request)
    {
        $token->revoker_id = $request->user()->id;
        $token->revoked_at = now();
        $token->status = 'revoked';
        $token->expires_at = $token->revoked_at; // tell Sanctum that this token is expired
        $token->save();

        $message = "Your token [$token->name] has been revoked.";
        // @TODO: remove LINE notify service
        /** @var User $user */
        $user = $token->tokenable;
        if (! $user->slack_webhook_url) {
            $user->notify(new LINEBaseNotification($message));
        } else {
            Http::post($user->slack_webhook_url, [
                'text' => $message,
            ]);
        }

        return back()->with('status', 'Token revoked successfully');
    }
}
