<?php

namespace App\Filament\Resources\FinancialDataResource\Pages;

use App\Filament\Resources\FinancialDataResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFinancialData extends CreateRecord
{ protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected static string $resource = FinancialDataResource::class;
}
