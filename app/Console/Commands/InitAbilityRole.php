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
            [ 'name' => 'create_app'] + $timestamps,
            [ 'name' => 'create_development_app'] + $timestamps,
            [ 'name' => 'create_production_app'] + $timestamps,
            [ 'name' => 'view_employee_sensitive_data'] + $timestamps,
            [ 'name' => 'view_patient_sensitive_data'] + $timestamps,
            [ 'name' => 'approve_request_form'] + $timestamps,
        ];
        Ability::query()->insert($abilities);

        $roles = [
            ['name' => 'root'] + $timestamps,
            ['name' => 'developer'] + $timestamps,
            ['name' => 'developer_office'] + $timestamps,
            ['name' => 'developer_healthcare'] + $timestamps,
            ['name' => 'dev_ops'] + $timestamps,
        ];
        Role::query()->insert($roles);

        $root = Role::query()->where('name', 'root')->first();
        Ability::query()->each(fn ($ability) => $root->allowTo($ability));

        $developer = Role::query()->where('name', 'developer')->first();
        $developer->allowTo('create_app');
        $developer->allowTo('create_development_app');

        $devOps = Role::query()->where('name', 'dev_ops')->first();
        $devOps->allowTo('create_app');
        $devOps->allowTo('create_development_app');
        $devOps->allowTo('create_production_app');

        $developerOffice = Role::query()->where('name', 'developer_office')->first();
        $developerOffice->allowTo('create_app');
        $developerOffice->allowTo('create_development_app');
        $developerOffice->allowTo('view_employee_sensitive_data');

        $developerHealthcare = Role::query()->where('name', 'developer_healthcare')->first();
        $developerHealthcare->allowTo('create_app');
        $developerHealthcare->allowTo('create_development_app');
        $developerHealthcare->allowTo('view_patient_sensitive_data');
    }
}
