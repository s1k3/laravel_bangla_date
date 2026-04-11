<?php

namespace S1K3\Bangla\Date;

use Illuminate\Support\ServiceProvider;

class BanglaDateServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/config/bangla_date.php' => config_path('bangla_date.php'),
        ], 'bangla-date-config');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config/bangla_date.php', 'bangla_date');
    }
}
