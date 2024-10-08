<?php

namespace App\Providers;

use App\Contracts\AdmissionAPI;
use App\Contracts\COVID19PCRLabAPI;
use App\Contracts\COVID19VaccinationAPI;
use App\Contracts\LabAPI;
use App\Contracts\PatientAPI;
use App\Contracts\UserAPI;
use App\Models\PersonalAccessToken;
use Hashids\Hashids;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Hashids::class, fn () => new Hashids(salt: config('app.key')));

        $this->app->bind(UserAPI::class, config('app.user_provider'));

        $this->app->bind(PatientAPI::class, config('app.patient_provider'));

        $this->app->bind(AdmissionAPI::class, config('app.admission_provider'));

        $this->app->bind(COVID19VaccinationAPI::class, config('app.covid19_vaccination_provider'));

        $this->app->bind(COVID19PCRLabAPI::class, config('app.covid19_pcr_lab_provider'));

        $this->app->bind(LabAPI::class, config('app.lab_provider'));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();

        Model::preventLazyLoading(! $this->app->isProduction());

        Model::preventAccessingMissingAttributes(! $this->app->isProduction());

        $threshold = config('app.query_time_threshold');
        DB::whenQueryingForLongerThan($threshold, function (Connection $connection, QueryExecuted $event) use ($threshold) {
            Log::warning("Database queries exceeded $threshold milliseconds ({$event->time} ms) on {$connection->getName()} : $event->sql");
        });

        Relation::enforceMorphMap([
            1 => 'App\Models\User',
        ]);

        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
