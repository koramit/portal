<?php

namespace App\Console\Commands;

use App\Models\Ability;
use App\Models\Role;
use Illuminate\Console\Command;

class DeployItemizeAPI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:itemize-api';

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
        if (Ability::query()->where('name', 'create_itemize_app')->first()) {
            return;
        }

        $itemizeAbility = Ability::query()->create(['name' => 'create_itemize_app']);
        $createAppAbility = Ability::query()->where('name', 'create_app')->first();

        $itemizeRole = Role::query()->create(['name' => 'itemize_developer']);
        $rootRole = Role::query()->where('name', 'root')->first();

        $itemizeRole->abilities()->attach([$createAppAbility->id, $itemizeAbility->id]);
        $rootRole->abilities()->attach($itemizeAbility);

        $rootRole->users()->each(fn ($user) => $user->flushPrivileges());
    }
}
