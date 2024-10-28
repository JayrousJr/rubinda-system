<?php

namespace App\Filament\Resources\FinancialDataResource\Pages;

use App\Filament\Resources\FinancialDataResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFinancialData extends ViewRecord
{
    protected static string $resource = FinancialDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
