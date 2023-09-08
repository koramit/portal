<?php

namespace App\Console\Commands;

use App\Models\Resources\Admission;
use Illuminate\Console\Command;

class UpdateAdmissionTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-admission-time';

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
        $first = Admission::query()->find(1);
        if (! $first || $first->updated_at->greaterThan('2023-01-01')) {
            $this->info('Already updated.');

            return;
        }

        Admission::query()
            ->where('id', '<=', 159696)
            ->each(function (Admission $admission) {
                $admission->admitted_at = $admission->admitted_at->addHours(7);
                if ($admission->discharged_at) {
                    $admission->discharged_at = $admission->discharged_at->addHours(7);
                }
                $admission->save();
                if ($admission->id % 1000 === 0) {
                    $this->info("$admission->id records updated.");
                }
            });

        $this->info('Done.');
    }
}
