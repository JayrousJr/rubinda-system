<?php

namespace App\Console\Commands;

use App\Models\Debt;
use Illuminate\Console\Command;

class DebtIncrease extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debts:increase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Increase over due debts by 1/30 of the original amount';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Retrieve debts that are overdue and still active
        $debts = Debt::where("status", "Active")
            ->whereDate("created_at", "<=", now()->subDays(90))
            ->whereColumn("remaining_debt", "<=", "total_debt")
            ->get();

        foreach ($debts as $debt) {
            $debt->incrementDebt();
        }

        $this->info("Overdue debts have been incremented where applicable.");
    }
}