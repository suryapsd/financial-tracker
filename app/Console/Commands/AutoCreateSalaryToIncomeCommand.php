<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Income;
use App\Models\CurrentSalary;
use Illuminate\Console\Command;

class AutoCreateSalaryToIncomeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-create-salary-to-income-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto create income for users based on frequency';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now();
        $todayFormatted = $today->format('Y-m-d');

        $currentSalaries = CurrentSalary::where('is_active', 1)
            ->where('is_auto_create_income', 1)
            // ->whereDate('effective_date', '<=', $todayFormatted)
            ->get();

        foreach ($currentSalaries as $item) {
            $userId = $item->user_id;
            $source = $item->income_source;
            $frequency = $item->frequency;

            // Query dasar
            $query = Income::where('user_id', $userId)
                ->where('source', $source)
                ->where('category_id', 1);

            // Filter berdasarkan frekuensi
            switch ($frequency) {
                case 'daily':
                    $query->whereDate('received_at', $todayFormatted);
                    break;

                case 'weekly':
                    $query->whereBetween('received_at', [
                        $today->copy()->startOfWeek(),
                        $today->copy()->endOfWeek()
                    ]);
                    break;

                case 'monthly':
                    $query->whereYear('received_at', $today->year)
                        ->whereMonth('received_at', $today->month);
                    break;

                case 'yearly':
                    $query->whereYear('received_at', $today->year);
                    break;
            }

            $alreadyExists = $query->exists();

            if (!$alreadyExists) {
                Income::create([
                    'user_id' => $userId,
                    'account_id' => $item->account_id,
                    'category_id' => 1,
                    'source' => $source,
                    'amount' => $item->amount,
                    'received_at' => $todayFormatted,
                    'description' => 'Salary from ' . $source . ' (' . $frequency . ' - ' . $todayFormatted . ')',
                ]);

                $this->info("automatically create income fo {$item->user->name}");
            }
        }

        return Command::SUCCESS;
    }
}
