<?php

namespace App\Console\Commands;

use App\Models\Ability;
use App\Models\Role;
use Illuminate\Console\Command;

class DeployPatientAllergyAPI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:patient-allergy-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle():void
    {
        if (Ability::query()->where('name', 'create_patient_allergy_app')->first()) {
            return;
        }

        $newAbility = Ability::query()->create(['name' => 'create_patient_allergy_app']);
        $createAppAbility = Ability::query()->where('name', 'create_app')->first();

        $newRole = Role::query()->create(['name' => 'patient_allergy_developer']);
        $rootRole = Role::query()->where('name', 'root')->first();

        $newRole->abilities()->attach([$createAppAbility->id, $newAbility->id]);
        $rootRole->abilities()->attach($newAbility);

        $rootRole->users()->each(fn ($user) => $user->flushPrivileges());
    }
}
