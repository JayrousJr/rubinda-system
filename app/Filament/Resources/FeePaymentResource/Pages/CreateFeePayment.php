<?php

namespace App\Filament\Resources\FeePaymentResource\Pages;

use App\Filament\Resources\FeePaymentResource;
use App\Models\FinancialData;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFeePayment extends CreateRecord
{
    protected static string $resource = FeePaymentResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $record = static::getModel()::create($data);

        $funds = FinancialData::findOrFail(1);
        $newTotal = $funds->total_amount + $data["amount"];
        $funds->update(["total_amount" => $newTotal]);

        return $record;
    }
}