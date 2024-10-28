<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\FeePayment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExpensesAndFee extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';
    protected static bool $isLazy = false;
    protected static ?int $sort = 0;
    protected function getStats(): array
    {
        $expenses = Expense::all()->sum("amount");
        $expenses = number_format(Expense::all()->sum("amount"), 0, ".", ",");
        $fees = number_format(FeePayment::all()->sum("amount"), 0, ".", ",");
        return [
            Stat::make('Jumla ya thamani ya Expenses', $expenses . " Tsh")
                ->description('matumizi')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Jumla ya thamani ya ada', $fees . " Tsh")
                ->description('50% increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }
}