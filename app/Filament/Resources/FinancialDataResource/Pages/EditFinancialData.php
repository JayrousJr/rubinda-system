<?php

namespace App\Filament\Resources\FinancialDataResource\Pages;

use App\Filament\Resources\FinancialDataResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFinancialData extends EditRecord
{
    protected static string $resource = FinancialDataResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}