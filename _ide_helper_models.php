<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
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
 * 
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
 * @property-read mixed $hashed_key
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

namespace App\Models\Resources{
/**
 * 
 *
 * @property string|null $age_unit
 * @property int|null $age
 * @property int $id
 * @property int $an
 * @property int $hn
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $dob
 * @property int $gender
 * @property string|null $discharge_type_name
 * @property string|null $discharge_status_name
 * @property \Illuminate\Support\Carbon|null $admitted_at
 * @property \Illuminate\Support\Carbon|null $discharged_at
 * @property int $ward_id
 * @property int $attending_staff_id
 * @property \Illuminate\Support\Carbon $checked_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Resources\AdmissionTransfer> $admissionTransfers
 * @property-read int|null $admission_transfers_count
 * @property-read \App\Models\Resources\Ward $ward
 * @method static \Illuminate\Database\Eloquent\Builder|Admission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Admission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Admission query()
 * @method static \Illuminate\Database\Eloquent\Builder|Admission whereAdmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admission whereAn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admission whereAttendingStaffId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admission whereCheckedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admission whereDischargeStatusName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admission whereDischargeTypeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admission whereDischargedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admission whereDob($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admission whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admission whereHn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admission whereWardId($value)
 */
	class Admission extends \Eloquent {}
}

namespace App\Models\Resources{
/**
 * 
 *
 * @property int $id
 * @property int $an
 * @property int $found
 * @property int $retry
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AdmissionCall newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdmissionCall newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdmissionCall query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdmissionCall whereAn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdmissionCall whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdmissionCall whereFound($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdmissionCall whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdmissionCall whereRetry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdmissionCall whereUpdatedAt($value)
 */
	class AdmissionCall extends \Eloquent {}
}

namespace App\Models\Resources{
/**
 * 
 *
 * @property int $id
 * @property int $admission_id
 * @property int $ward_id
 * @property int $attending_staff_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Resources\Ward $ward
 * @method static \Illuminate\Database\Eloquent\Builder|AdmissionTransfer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdmissionTransfer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdmissionTransfer query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdmissionTransfer whereAdmissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdmissionTransfer whereAttendingStaffId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdmissionTransfer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdmissionTransfer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdmissionTransfer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdmissionTransfer whereWardId($value)
 */
	class AdmissionTransfer extends \Eloquent {}
}

namespace App\Models\Resources{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $license_no
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AttendingStaff newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendingStaff newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendingStaff query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendingStaff whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendingStaff whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendingStaff whereLicenseNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendingStaff whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendingStaff whereUpdatedAt($value)
 */
	class AttendingStaff extends \Eloquent {}
}

namespace App\Models\Resources{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $name_short
 * @property string|null $number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Resources\Admission> $admissions
 * @property-read int|null $admissions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Ward newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ward newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ward query()
 * @method static \Illuminate\Database\Eloquent\Builder|Ward whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ward whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ward whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ward whereNameShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ward whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ward whereUpdatedAt($value)
 */
	class Ward extends \Eloquent {}
}

namespace App\Models{
/**
 * 
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
 * 
 *
 * @property int $id
 * @property int $personal_access_token_id
 * @property string $route
 * @property \Illuminate\Database\Eloquent\Casts\AsEncryptedArrayObject $payload
 * @property int $found
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\PersonalAccessToken $personalAccessToken
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
 * 
 *
 * @property int $id
 * @property int $requester_id
 * @property \Illuminate\Support\Collection $form
 * @property int|null $authority_id
 * @property int|null $revoke_authority_id
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $authority
 * @property-read mixed $hashed_key
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
 * 
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
 * @property \Illuminate\Database\Eloquent\Casts\AsEncryptedArrayObject|null $profile
 * @property-read mixed $abilities
 * @property-read mixed $abilities_id
 * @property-read mixed $hashed_key
 * @property-read mixed $line_notify_enabled
 * @property-read mixed $notifiable
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read mixed $role_labels
 * @property-read mixed $role_names
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read mixed $slack_webhook_url
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
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

