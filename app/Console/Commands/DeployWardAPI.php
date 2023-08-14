<?php

namespace App\Console\Commands;

use App\Models\Ability;
use App\Models\Role;
use Illuminate\Console\Command;

class DeployWardAPI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:ward-api';

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
        if (Ability::query()->where('name', 'create_ward_app')->first()) {
            return;
        }

        $wardAbility = Ability::query()->create(['name' => 'create_ward_app']);
        $createAppAbility = Ability::query()->where('name', 'create_app')->first();

        $wardRole = Role::query()->create(['name' => 'ward_developer']);
        $rootRole = Role::query()->where('name', 'root')->first();

        $wardRole->abilities()->attach([$createAppAbility->id, $wardAbility->id]);
        $rootRole->abilities()->attach($wardAbility);

        $rootRole->users()->each(fn ($user) => $user->flushPrivileges());
    }
}
