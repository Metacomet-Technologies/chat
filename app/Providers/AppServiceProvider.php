<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    private bool $isProduction = false;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->isProduction = $this->app->environment('production');
        $this->configureCommands();
        $this->configureModel();
        $this->configureVite();
        $this->configureURL();
    }

    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands($this->isProduction);
    }

    private function configureModel(): void
    {
        Model::preventLazyLoading(! $this->isProduction);
        Model::preventSilentlyDiscardingAttributes(! $this->isProduction);
        Model::preventAccessingMissingAttributes(! $this->isProduction);
        Model::shouldBeStrict();
        Model::unguard();
    }

    private function configureVite(): void
    {
        Vite::prefetch(concurrency: 5);
    }

    private function configureURL(): void
    {
        URL::forceScheme('https');
    }
}
