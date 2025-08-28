<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateFinancialReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $user;
    protected $month;
    protected $year;

    public $tries = 3; // coba ulang max 3x kalau gagal
    public $timeout = 120; // max 120 detik per job

    public function __construct(User $user, int $month, int $year)
    {
        $this->user = $user;
        $this->month = $month;
        $this->year = $year;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->user->checkFinancialReports($this->month, $this->year);
    }
}
