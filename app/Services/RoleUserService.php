<?php

namespace App\Services;

use App\Models\Role;
use App\Models\ServiceRequestForm;
use App\Models\User;
use Illuminate\Support\Collection;

class RoleUserService
{
    const SERVICES = [
        ['label' => 'Authentication', 'name' => 'authenticate_developer'],
        ['label' => 'Authentication with sensitive data', 'name' => 'authenticate_sensitive_data_developer'],
        ['label' => 'Patient data', 'name' => 'patient_developer'],
        ['label' => 'Patient sensitive data', 'name' => 'patient_sensitive_data_developer'],
        ['label' => 'Admission data', 'name' => 'admission_developer'],
        ['label' => 'COVID-19 Vaccine/PCR', 'name' => 'covid_developer'],
        ['label' => 'Service without rate limit', 'name' => 'dev_ops'],
        ['label' => 'Ward data', 'name' => 'ward_developer'],
        ['label' => 'Item master', 'name' => 'itemize_developer'],
        ['label' => 'Lab', 'name' => 'lab_developer'],
        ['label' => 'Patient Allergy', 'name' => 'patient_allergy_developer'],
    ];

    const FORM_RULES = [
        'authenticate_developer' => ['sometimes', 'accepted'],
        'authenticate_sensitive_data_developer' => ['sometimes', 'accepted'],
        'patient_developer' => ['sometimes', 'accepted'],
        'patient_sensitive_data_developer' => ['sometimes', 'accepted'],
        'admission_developer' => ['sometimes', 'accepted'],
        'covid_developer' => ['sometimes', 'accepted'],
        'dev_ops' => ['sometimes', 'accepted'],
        'note' => ['required', 'string', 'min:128', 'max:1024'],
        'ward_developer' => ['sometimes', 'accepted'],
        'itemize_developer' => ['sometimes', 'accepted'],
        'lab_developer' => ['sometimes', 'accepted'],
        'patient_allergy_developer' => ['sometimes', 'accepted'],
    ];

    const TOKEN_ABILITIES = [
        ['ability' => 'user:data', 'name' => 'user_data', 'label' => 'User', 'can' => 'create_authenticate_app'],
        ['ability' => 'user:authenticate', 'name' => 'user_authenticate', 'label' => 'Authenticate', 'can' => 'create_authenticate_app'],
        ['ability' => 'patient:data', 'name' => 'patient_data', 'label' => 'Patient', 'can' => 'create_patient_app'],
        ['ability' => 'admission:data', 'name' => 'admission_data', 'label' => 'Admission', 'can' => 'create_admission_app'],
        ['ability' => 'patient:admissions', 'name' => 'patient_admissions', 'label' => 'Patient admissions', 'can' => 'create_admission_app'],
        ['ability' => 'patient:recently-admission', 'name' => 'patient_recently_admission', 'label' => 'Patient recently admission', 'can' => 'create_admission_app'],
        ['ability' => 'covid19:pcr-labs', 'name' => 'covid19_pcr_labs', 'label' => 'COVID-19 PCR labs', 'can' => 'create_covid_lab_pcr_app'],
        ['ability' => 'covid19:vaccinations', 'name' => 'covid19_vaccinations', 'label' => 'COVID-19 vaccinations', 'can' => 'create_covid_vaccine_app'],
        ['ability' => 'user:sensitive-data', 'name' => 'user_sensitive_data', 'label' => 'User sensitive data', 'can' => 'view_user_sensitive_data'],
        ['ability' => 'patient:sensitive-data', 'name' => 'patient_sensitive_data', 'label' => 'Patient sensitive data', 'can' => 'view_patient_sensitive_data'],
        ['ability' => 'rate-limit:none', 'name' => 'rate_limit_none', 'label' => 'No rate limit', 'can' => 'create_no_rate_limit_app'],
        ['ability' => 'ward:admissions', 'name' => 'ward_admissions', 'label' => 'Ward data', 'can' => 'create_ward_app'],
        ['ability' => 'item:master', 'name' => 'item_master', 'label' => 'Item master', 'can' => 'create_itemize_app'],
        ['ability' => 'lab:pending', 'name' => 'lab_pending', 'label' => 'Lab pending', 'can' => 'create_lab_app'],
        ['ability' => 'lab:results', 'name' => 'lab_results', 'label' => 'Lab results', 'can' => 'create_lab_app'],
        ['ability' => 'patient:allergy', 'name' => 'patient_allergy', 'label' => 'Patient allergy', 'can' => 'create_patient_allergy_app'],
    ];

    public function attachRoles(ServiceRequestForm $form): void
    {
        $form->load('requester');
        $requester = $form->requester;

        $roleNames = collect(self::SERVICES)->pluck('name');
        $approvedRoles = $form->form
            ->filter(fn ($value, $key) => $roleNames->contains($key) && $value === true)
            ->keys();

        Role::query()->whereIn('name', $approvedRoles)->each(fn ($role) => $requester->attachRole($role));
    }

    public function detachRoles(ServiceRequestForm $form): void
    {
        $form->load('requester');
        $requester = $form->requester;

        $roleNames = collect(self::SERVICES)->pluck('name');
        $approvedRoles = $form->form
            ->filter(fn ($value, $key) => $roleNames->contains($key) && $value === true)
            ->keys();

        Role::query()->whereIn('name', $approvedRoles)->each(fn ($role) => $requester->detachRole($role));
    }

    public function availableServices(User $user): Collection
    {
        return collect(RoleUserService::TOKEN_ABILITIES)
            ->map(function ($ability) use ($user) {
                $ability['can'] = $user->can($ability['can']);

                return $ability;
            })
            ->filter(fn ($ability) => $ability['can'] === true);
    }

    public function getAbilityValidationRules(User $user): Collection
    {
        return $this->availableServices($user)
            ->pluck('name')
            ->map(fn ($name) => [$name => ['sometimes', 'accepted']])
            ->collapse();
    }
}
