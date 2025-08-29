<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use App\Jobs\GenerateFinancialReportJob;

class CheckFinancial extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-financial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();
        $year = now()->year;
        $month = now()->month;
        $delaySeconds = 0;

        foreach ($users as $user) {
            // for ($month = 1; $month <= 12; $month++) {
            //     GenerateFinancialReportJob::dispatch($user, $month, $year)
            //         ->delay(now()->addSeconds($delaySeconds));
            //     $delaySeconds += 30;
            // }
            GenerateFinancialReportJob::dispatch($user, $month, $year)
                ->delay(now()->addSeconds($delaySeconds));
            $delaySeconds += 30;

            $this->info("Queued financial reports for {$user->name}");
        }

        return Command::SUCCESS;
    }
}
