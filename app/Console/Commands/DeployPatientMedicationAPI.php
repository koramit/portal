<?php

namespace App\Console\Commands;

use App\Models\Ability;
use App\Models\Role;
use Illuminate\Console\Command;

class DeployPatientMedicationAPI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:patient-medication-api';

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
        if (Ability::query()->where('name', 'create_patient_medication_app')->first()) {
            return;
        }

        $newAbility = Ability::query()->create(['name' => 'create_patient_medication_app']);
        $createAppAbility = Ability::query()->where('name', 'create_app')->first();

        $newRole = Role::query()->create(['name' => 'patient_medication_developer']);
        $rootRole = Role::query()->where('name', 'root')->first();

        $newRole->abilities()->attach([$createAppAbility->id, $newAbility->id]);
        $rootRole->abilities()->attach($newAbility);

        $rootRole->users()->each(fn ($user) => $user->flushPrivileges());
    }
}
