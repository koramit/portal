<?php

namespace App\Console\Commands;

use App\APIs\PatientAPI;
use App\Models\PersonalAccessToken;
use App\Models\Resources\AdmissionCall;
use App\Models\User;
use App\Services\AdmissionManager;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class BuildAdmissionList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admission:build-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected int $LIMIT_CASES = 25;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $ans = $this->getList();

        $api = new PatientAPI;
        $manager = new AdmissionManager;
        $founds = collect();
        /** @var PersonalAccessToken $token */
        $token = User::query()
            ->where('name', 'system')
            ->where('full_name', 'system')
            ->first()
            ->tokens()
            ->first();
        for ($i = 0; $i < $ans->count(); $i++) {
            $admit = $api->getAdmission($ans[$i], true);
            $token->serviceAccessLogs()->create([
                'payload' => ['an' => $ans[$i]],
                'route' => 'api.admission',
                'found' => $admit['found'] ?? false,
            ]);
            if (! ($admit['ok'] ?? false) || ! ($admit['found'] ?? false)) {
                $call = AdmissionCall::query()->where('an', $ans[$i])->first();
                $call->retry = $call->retry + 1;
                $call->save();

                continue;
            }

            $founds->push($ans[$i]);
            $manager->manage($admit);
        }

        AdmissionCall::query()->whereIn('an', $founds)->update(['found' => true]);
    }

    protected function getList(): Collection
    {
        $ans = AdmissionCall::query()
            ->where('found', false)
            ->where('retry', '<', 50) // ~ 1 day
            ->limit($this->LIMIT_CASES)
            ->pluck('an');

        $count = $ans->count();
        if ($count === $this->LIMIT_CASES) {
            return $ans;
        }

        $max = AdmissionCall::query()->max('an');
        for ($i = 1; $i <= ($this->LIMIT_CASES - $count); $i++) {
            $an = $max + $i;
            $ans->push($an);
            AdmissionCall::query()
                ->create(['an' => $an]);
        }

        return $ans;
    }
}
