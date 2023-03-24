<?php

namespace App\Providers;

use Hashids\Hashids;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Hashids::class, fn () => new Hashids(salt: config('app.key')));
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
            Log::warning("Database queries exceeded $threshold milliseconds on {$connection->getName()} : $event->sql");
        });
    }
}
