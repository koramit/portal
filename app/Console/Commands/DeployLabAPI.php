<?php

namespace App\Console\Commands;

use App\Models\Ability;
use App\Models\Role;
use Illuminate\Console\Command;

class DeployLabAPI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:lab-api';

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
        if (Ability::query()->where('name', 'create_lab_app')->first()) {
            return;
        }

        $labAbility = Ability::query()->create(['name' => 'create_lab_app']);
        $createAppAbility = Ability::query()->where('name', 'create_app')->first();

        $labRole = Role::query()->create(['name' => 'lab_developer']);
        $rootRole = Role::query()->where('name', 'root')->first();

        $labRole->abilities()->attach([$createAppAbility->id, $labAbility->id]);
        $rootRole->abilities()->attach($labAbility);

        $rootRole->users()->each(fn ($user) => $user->flushPrivileges());
    }
}
