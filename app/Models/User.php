<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\PKHashable;
use Illuminate\Database\Eloquent\Casts\AsEncryptedArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, PKHashable;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expire_at' => 'datetime',
        'line_notify_token' => 'encrypted',
        'profile' => AsEncryptedArrayObject::class,
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    /** @alias $abilities */
    protected function abilities(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cacheAbilities("uid-$this->id-abilities", 'name'),
        );
    }

    /** @alias $abilities_id */
    protected function abilitiesId(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cacheAbilities("uid-$this->id-abilities-id", 'id'),
        );
    }

    /** @alias $role_names*/
    protected function roleNames(): Attribute
    {
        return Attribute::make(
            get: fn () => cache()->remember("uid-$this->id-role-names", config('session.lifetime') * 60, function () {
                return $this->roles()->pluck('name');
            }),
        );
    }

    /** @alias string $role_labels*/
    protected function roleLabels(): Attribute
    {
        return Attribute::make(
            get: fn () => cache()->remember("uid-$this->id-role-labels", config('session.lifetime') * 60, function () {
                return $this->roles()->whereNotNull('label')->pluck('label');
            }),
        );
    }

    public function slackWebhookUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->profile['slack_webhook_url'] ?? null,
        );
    }

    public function notifiable(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->slack_webhook_url,
        );
    }

    public function attachRole(Role $role): void
    {
        $this->roles()->syncWithoutDetaching($role);
        $this->flushPrivileges();
    }

    public function detachRole(Role $role): void
    {
        $this->roles()->detach($role);
        $this->flushPrivileges();
    }

    public function hasAbility(string|int $ability): bool
    {
        $abilities = (gettype($ability) === 'integer')
            ? $this->cacheAbilities("uid-$this->id-abilities-id", 'id')
            : $this->abilities;

        return $abilities->contains($ability);
    }

    public function hasRole(string $name): bool
    {
        return $this->role_names->contains($name);
    }

    protected function cacheAbilities(string $key, string $field)
    {
        return cache()->remember($key, config('session.lifetime') * 60, function () use ($field) {
            $this->refresh(); // reload for new role

            // if unique() is not activated then the output is an array
            // but the output is an associated array so, provide
            // flatten() to guarantee output always an array
            return $this->roles()->with('abilities')->get()->map->abilities->flatten()->pluck($field)->unique()->flatten();
        });
    }

    public function flushPrivileges(): void
    {
        cache()->forget("uid-$this->id-abilities");
        cache()->forget("uid-$this->id-role-names");
        cache()->forget("uid-$this->id-role-labels");
        cache()->forget("uid-$this->id-abilities-id");
    }
}
