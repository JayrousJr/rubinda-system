<?php

namespace App\Filament\Resources\DebtResource\Pages;

use App\Exports\DebtsExport;
use App\Filament\Resources\DebtResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;

class ListDebts extends ListRecords
{
    protected static string $resource = DebtResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('export_debt')
                ->label("Export Debts")
                ->action(function () {
                    $fileName = "Madeni_" . now()->format('Y_m_d') . ".xlsx";
                    return Excel::download(new DebtsExport, $fileName);
                })
                ->requiresConfirmation()
                ->icon('heroicon-o-arrow-down-tray'),
        ];
    }
}