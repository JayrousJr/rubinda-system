<?php

namespace App\Filament\Resources\LoanApplicationResource\Pages;

use App\Filament\Resources\LoanApplicationResource;
use Filament\Actions;
use Filament\Pages\Actions\ButtonAction;
use Filament\Resources\Pages\CreateRecord;

class CreateLoanApplication extends CreateRecord
{
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected static string $resource = LoanApplicationResource::class;

    protected function getFormActions(): array
    {
        return [
            // Example of a custom button
            ButtonAction::make('Custom Action')
                ->label('Save')
                ->action('save')
                ->color('primary'),
        ];
    }
}