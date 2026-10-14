<?php

namespace App\Providers;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        DB::whenQueryingForLongerThan(500, function (Connection $connection) {
            // slow query monitoring hook — log if needed
        });

        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA journal_mode=WAL;');
            DB::statement('PRAGMA busy_timeout=5000;');
            DB::statement('PRAGMA foreign_keys=ON;');
        }
    }
}
