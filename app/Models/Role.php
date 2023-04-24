<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    public function abilities(): BelongsToMany
    {
        return $this->belongsToMany(Ability::class)->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function allowTo(Ability|string $ability)
    {
        if (is_string($ability)) {
            $ability = Ability::where('name', $ability)->firstOrCreate(['name' => $ability]);
        }

        $this->abilities()->syncWithoutDetaching($ability);

        // effect the new ability
        $userIds = $this->users()->select('id')->pluck('id');
        foreach ($userIds as $id) {
            cache()->forget("uid-{$id}-abilities");
        }
    }
}
