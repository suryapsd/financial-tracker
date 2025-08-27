<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Panel;
use App\Services\GeminiAIService;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
        // return str_ends_with($this->email, '@yourdomain.com') && $this->hasVerifiedEmail();
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function debts()
    {
        return $this->hasMany(Debt::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function savings()
    {
        return $this->hasMany(Saving::class);
    }

    public function emergencyFunds()
    {
        return $this->hasMany(EmergencyFund::class);
    }

    public function budgetPlans()
    {
        return $this->hasMany(BudgetPlan::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function determineFinancialLevel()
    {
        $assets = $this->assets()->sum('value');
        $debts = $this->debts()->sum('amount');
        $emergencyFundPlan = $this->emergencyFunds()->sum('target_amount');
        $emergencyFundCurrent = $this->emergencyFunds()->sum('current_amount');

        $retirementFundPlan = $this->budgetPlans()->where('category_id', 38)->sum('planned_amount');
        $retirementFundCurrent = $this->budgetPlans()->where('category_id', 38)->sum('actual_amount');

        $inheritancePlan = $this->budgetPlans()->where('category_id', 39)->sum('planned_amount');
        $inheritancePlanCurrent = $this->budgetPlans()->where('category_id', 39)->sum('actual_amount');

        $netWorth = $assets - $debts;

        $monthlyIncome = $this->incomes()->whereMonth('received_at', now()->subMonth()->month)->sum('amount') ?? 0;
        $monthlyExpenses = $this->expenses()->whereMonth('expense_date', now()->subMonth()->month)->sum('amount');

        // Level 0: 💥 Bankrupt (Assets < Debts)
        if ($assets < $debts) {
            return 0;
        }

        // Level 1: 🔗 Trapped in Debt (Debts > Net Worth)
        if ($debts > $netWorth) {
            return 1;
        }

        // Level 2: 🎭 Looks Rich (Assets > 0 but < Debts)
        if ($assets > 0 && $assets < $debts) {
            return 2;
        }

        // Level 3: 💸 Paycheck to Paycheck
        if ($monthlyIncome > 0 && $monthlyExpenses >= $monthlyIncome * 0.9) {
            return 3;
        }

        // Level 4: 🌸 Has Emergency Fund (≥ 3x pengeluaran)
        if (
            $emergencyFundCurrent >= $emergencyFundPlan &&
            $emergencyFundCurrent >= ($monthlyExpenses * 3)
        ) {
            return 4;
        }

        // Level 5: 💰 Retirement Fund (actual > plan)
        if ($retirementFundCurrent >= $retirementFundPlan && $retirementFundPlan > 0) {
            return 5;
        }

        // Level 6: 🏆 Has Inheritance (actual > plan)
        if ($inheritancePlanCurrent >= $inheritancePlan && $inheritancePlan > 0) {
            return 6;
        }

        // Default fallback
        return 0;
    }

    public function financialReports()
    {
        return $this->hasMany(FinancialReport::class);
    }

    public function checkFinancialReports($month = null, $year = null)
    {
        $month = $month ?? request('month') ?? now()->month;
        $year = $year ?? request('year') ?? now()->year;

        // Summary Values
        $accountsTotal = $this->accounts()->sum('balance');
        $assetsTotal = $this->assets()->sum('value');
        $debtsTotal = $this->debts()->sum('amount');
        $netWorth = $accountsTotal + $assetsTotal - $debtsTotal;

        $monthlyIncome = $this->incomes()
            ->whereMonth('received_at', $month)
            ->whereYear('received_at', $year)
            ->sum('amount') ?? 0;

        $monthlyExpenses = $this->expenses()
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->sum('amount') ?? 0;

        $cashFlow = $monthlyIncome - $monthlyExpenses;

        $emergencyFundCurrent = $this->emergencyFunds()->sum('current_amount');
        $emergencyFundTarget = $this->emergencyFunds()->sum('target_amount');

        $hasInvestment = $this->assets()->exists();
        $hasRetirementPlan = $this->budgetPlans()->where('category_id', 38)->exists();
        $hasDependents = true;

        // Format data untuk AI checkup
        $financialData = [
            'monthly_income' => $monthlyIncome,
            'monthly_expenses' => $monthlyExpenses,
            'emergency_fund_current' => $emergencyFundCurrent,
            'emergency_fund_target' => $emergencyFundTarget,
            'assets_total' => $assetsTotal,
            'debts_total' => $debtsTotal,
            'net_worth' => $netWorth,
            'has_investment' => $hasInvestment,
            'has_retirement_plan' => $hasRetirementPlan,
            'has_dependents' => $hasDependents,
        ];

        $gemini = new GeminiAIService();
        $checkUpFinanceAI = $gemini->checkUpFinanceAI(json_encode($financialData, JSON_PRETTY_PRINT));

        // Check account balance integrity
        $accountBalance = $this->accounts->sum('balance');
        $totalIncomeAll = $this->financialReports->sum('total_income');
        $totalExpenseAll = $this->financialReports->sum('total_expense');
        $isBalanceAccurate = $accountBalance === ($totalIncomeAll - $totalExpenseAll);

        // Store or update report
        $financialReport = FinancialReport::updateOrCreate(
            [
                'user_id' => $this->id,
                'month' => $month,
                'year' => $year,
            ],
            [
                'total_income' => $monthlyIncome,
                'total_expense' => $monthlyExpenses,
                'net_worth' => $netWorth,
                'cash_flow' => $cashFlow,
                'asset_value' => $assetsTotal,
                'debt_value' => $debtsTotal,
                'summary' => $checkUpFinanceAI,
                'note' => json_encode([
                    'account_balance' => $isBalanceAccurate
                ]),
            ]
        );

        return $financialReport;
    }
}
