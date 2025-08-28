<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

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
        foreach ($users as $user) {
            for ($month = 1; $month <= 12; $month++) {
                $user->checkFinancialReports($month, $year);
            }
            $this->info("generate financial report for {$user->name}");
        }
        return Command::SUCCESS;
    }
}
