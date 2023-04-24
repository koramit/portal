<?php

namespace App\Console\Commands;

use App\Models\Ability;
use App\Models\Role;
use Illuminate\Console\Command;

class InitAbilityRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:ability-role';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Init ability and role');
        $timestamps = ['created_at' => now(), 'updated_at' => now()];
        $abilities = [
            ['name' => 'create_app'] + $timestamps,
            ['name' => 'create_authenticate_app'] + $timestamps,
            ['name' => 'create_patient_app'] + $timestamps,
            ['name' => 'create_admission_app'] + $timestamps,
            ['name' => 'create_covid_lab_pcr_app'] + $timestamps,
            ['name' => 'create_covid_vaccine_app'] + $timestamps,
            ['name' => 'create_no_rate_limit_app'] + $timestamps,
            ['name' => 'view_user_sensitive_data'] + $timestamps,
            ['name' => 'view_patient_sensitive_data'] + $timestamps,
            ['name' => 'approve_request_form'] + $timestamps,
            ['name' => 'revoke_any_tokens'] + $timestamps,
        ];
        Ability::query()->insert($abilities);

        $roles = [
            ['name' => 'root'] + $timestamps,
            ['name' => 'authenticate_developer'] + $timestamps,
            ['name' => 'authenticate_sensitive_data_developer'] + $timestamps,
            ['name' => 'patient_developer'] + $timestamps,
            ['name' => 'patient_sensitive_data_developer'] + $timestamps,
            ['name' => 'admission_developer'] + $timestamps,
            ['name' => 'covid_developer'] + $timestamps,
            ['name' => 'dev_ops'] + $timestamps,
        ];
        Role::query()->insert($roles);

        $root = Role::query()->where('name', 'root')->first();
        Ability::query()->each(fn ($ability) => $root->allowTo($ability));

        $devOps = Role::query()->where('name', 'dev_ops')->first();
        $devOps->allowTo('create_app');
        $devOps->allowTo('create_no_rate_limit_app');

        $developerOffice = Role::query()->where('name', 'authenticate_developer')->first();
        $developerOffice->allowTo('create_app');
        $developerOffice->allowTo('create_authenticate_app');

        $developerOffice = Role::query()->where('name', 'authenticate_sensitive_data_developer')->first();
        $developerOffice->allowTo('create_app');
        $developerOffice->allowTo('create_authenticate_app');
        $developerOffice->allowTo('view_user_sensitive_data');

        $developerHealthcare = Role::query()->where('name', 'patient_developer')->first();
        $developerHealthcare->allowTo('create_app');
        $developerHealthcare->allowTo('create_patient_app');

        $developerHealthcare = Role::query()->where('name', 'admission_developer')->first();
        $developerHealthcare->allowTo('create_app');
        $developerHealthcare->allowTo('create_admission_app');

        $developerHealthcare = Role::query()->where('name', 'patient_sensitive_data_developer')->first();
        $developerHealthcare->allowTo('create_app');
        $developerHealthcare->allowTo('view_patient_sensitive_data');

        $developerHealthcare = Role::query()->where('name', 'covid_developer')->first();
        $developerHealthcare->allowTo('create_app');
        $developerHealthcare->allowTo('create_covid_lab_pcr_app');
        $developerHealthcare->allowTo('create_covid_vaccine_app');
    }
}
