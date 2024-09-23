<?php

namespace App\Console\Commands;

use App\APIs\PatientAPI;
use App\Models\PersonalAccessToken;
use App\Models\Resources\Admission;
use App\Models\User;
use App\Services\AdmissionManager;
use Illuminate\Console\Command;

class UpdateAdmissionList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admission:update-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected int $LIMIT_CASES = 300;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $api = new PatientAPI;
        $manager = new AdmissionManager;
        /** @var PersonalAccessToken $token */
        $token = User::query()
            ->where('name', 'system')
            ->where('full_name', 'system')
            ->first()
            ->tokens()
            ->first();

        Admission::query()
            ->whereNull('discharged_at')
            ->orderBy('checked_at')
            ->limit($this->LIMIT_CASES)
            ->pluck('an')
            ->each(function ($an) use (&$api, &$manager, &$token) {
                $admit = $api->getAdmission($an, false);
                $token->serviceAccessLogs()->create([
                    'payload' => ['an' => $an],
                    'route' => 'api.admission',
                    'found' => $admit['found'] ?? false,
                ]);

                if (($admit['ok'] ?? false) && ($admit['found'] ?? false)) {
                    $manager->manage($admit);
                }
            });
    }
}
