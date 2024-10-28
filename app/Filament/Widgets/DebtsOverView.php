<?php

namespace App\Filament\Widgets;

use App\Models\Debt;
use App\Models\LoanApplication;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DebtsOverView extends BaseWidget
{
    protected static bool $isLazy = false;
    protected static ?int $sort = -1;

    protected function getStats(): array
    {
        $debts = Debt::where("status", "Active");
        $debtsCount = $debts->count();
        $debtAmount = number_format($debts->sum("total_debt"), 0, ".", ",");
        $loanRequests = LoanApplication::where("status", "Pending")->count();
        return [
            Stat::make('Jumla ya Madeni', $debtsCount)
                ->description('jumla ya madeni')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Maombi ya Mikopo', $loanRequests)
                ->description('maombi')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('warning'),
            Stat::make('Thamani ya madeni', $debtAmount . " Tsh")
                ->description('thamani ya madeni')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }
}