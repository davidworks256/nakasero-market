<?php

namespace App\Providers;

use App\Models\Listing;
use App\Observers\ListingObserver;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Listing::observe(ListingObserver::class);

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        if (config('database.default') === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database');
            if ($dbPath && ! file_exists($dbPath) && $dbPath !== ':memory:') {
                $dir = dirname($dbPath);
                if (! file_exists($dir)) {
                    mkdir($dir, 0755, true);
                }
                touch($dbPath);
            }
        }

        try {
            if (! Schema::hasTable('users')) {
                Artisan::call('migrate', ['--force' => true]);
                Artisan::call('db:seed', ['--force' => true]);
            }
        } catch (\Throwable $e) {
            // Avoid failing boot cycle if external DB host is cold
        }
    }
}
