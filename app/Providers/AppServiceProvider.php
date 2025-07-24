<?php

namespace App\Providers;

use Spatie\Health\Facades\Health;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Spatie\Health\Checks\Checks\PingCheck;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\DatabaseSizeCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Checks\Checks\DatabaseConnectionCountCheck;

class AppServiceProvider extends ServiceProvider
{
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
        Health::checks([
            OptimizedAppCheck::new(),
            DebugModeCheck::new(),
            EnvironmentCheck::new(),
            DatabaseConnectionCountCheck::new(),
            DatabaseSizeCheck::new(),
            QueueCheck::new(),
            UsedDiskSpaceCheck::new(),
            PingCheck::new()
                ->url('https://balipinnacle.com'),
            ScheduleCheck::new(),
        ]);
        Blade::directive('money', function ($amount) {
            return "<?php echo 'IDR ' . number_format($amount, 0, ',', '.'); ?>";
        });
    }
}
