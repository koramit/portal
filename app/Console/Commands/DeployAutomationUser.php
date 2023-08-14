<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DeployAutomationUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:automation-user';

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
        if (User::query()->where('full_name', 'system')->first()) {
            return;
        }

        $user = User::query()
            ->create([
                'name' => 'system',
                'full_name' => 'system',
                'password' => Hash::make(Str::random()),
                'expire_at' => now()->addYears(10),
            ]);

        $user->createToken(
            'automation',
            [],
            now(),
        );
    }
}
