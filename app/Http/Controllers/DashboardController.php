<?php

namespace App\Http\Controllers;

use App\Models\PersonalAccessToken;
use App\Services\RoleUserService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $tokens = PersonalAccessToken::query()
            ->when($user->cannot('revoke_any_tokens'), fn ($query) => $query->where('tokenable_id', $user->id))
            ->when($user->can('revoke_any_tokens'), fn ($query) => $query->with('tokenable'))
            ->withCount('serviceAccessLogs')
            ->paginate();

        return view('dashboard')->with([
            'title' => 'Dashboard',
            'appTokens' => $tokens,
            'tokenAbilityLabelMapping' => collect(RoleUserService::TOKEN_ABILITIES)->pluck('label', 'ability'),
        ]);
    }
}
