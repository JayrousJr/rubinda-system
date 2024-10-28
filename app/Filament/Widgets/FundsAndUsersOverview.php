<?php

namespace App\Filament\Widgets;

use App\Models\FinancialData;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class FundsAndUsersOverview extends BaseWidget
{
    protected static ?int $sort = -2;
    protected static bool $isLazy = false;
    protected function getStats(): array
    {
        $users = DB::table("users")->count();
        $funds = FinancialData::findOrFail(1)->total_amount;
        $interest_rate = FinancialData::findOrFail(1)->interest_rate;
        return [
            Stat::make('Jumla ya wanachama', $users)
                ->description('50% increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Riba ya Mkopo', $interest_rate . "%")
                ->description('50% idecrease')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
            Stat::make('Akiba Iliyopo', number_format($funds, 0, ".", ",") . " Tsh")
                ->description('60% increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
        ];
    }
}