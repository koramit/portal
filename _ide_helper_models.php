<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Ability
 *
 * @property int $id
 * @property string $name
 * @property string|null $label
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|Ability newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ability newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ability query()
 * @method static \Illuminate\Database\Eloquent\Builder|Ability whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ability whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ability whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ability whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ability whereUpdatedAt($value)
 */
	class Ability extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PersonalAccessToken
 *
 * @property int $id
 * @property int $tokenable_type
 * @property int $tokenable_id
 * @property string $name
 * @property string $token
 * @property array|null $abilities
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property string|null $revoked_at
 * @property int $status
 * @property int|null $revoker_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ServiceAccessLog> $serviceAccessLogs
 * @property-read int|null $service_access_logs_count
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $tokenable
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken active()
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken findByUnhashKey(string $hashed, ?string $field = 'id')
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereAbilities($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereRevokedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereRevokerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereTokenableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereTokenableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereUpdatedAt($value)
 */
	class PersonalAccessToken extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Role
 *
 * @property int $id
 * @property string $name
 * @property string|null $label
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ability> $abilities
 * @property-read int|null $abilities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ServiceAccessLog
 *
 * @property int $id
 * @property int $personal_access_token_id
 * @property string $route
 * @property \Illuminate\Database\Eloquent\Casts\AsEncryptedArrayObject $payload
 * @property int $found
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\PersonalAccessToken|null $personalAccessToken
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAccessLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAccessLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAccessLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAccessLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAccessLog whereFound($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAccessLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAccessLog wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAccessLog wherePersonalAccessTokenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAccessLog whereRoute($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAccessLog whereUpdatedAt($value)
 */
	class ServiceAccessLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ServiceRequestForm
 *
 * @property int $id
 * @property int $requester_id
 * @property \Illuminate\Database\Eloquent\Casts\AsCollection $form
 * @property int|null $authority_id
 * @property int|null $revoke_authority_id
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $authority
 * @property-read \App\Models\User|null $requester
 * @property-read \App\Models\User|null $revokeAuthority
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm findByUnhashKey(string $hashed, ?string $field = 'id')
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm whereAuthorityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm whereForm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm whereRequesterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm whereRevokeAuthorityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm whereUpdatedAt($value)
 */
	class ServiceRequestForm extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $full_name
 * @property string $password
 * @property \Illuminate\Support\Carbon|null $expire_at
 * @property mixed|null $line_notify_token
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User findByUnhashKey(string $hashed, ?string $field = 'id')
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereExpireAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLineNotifyToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

