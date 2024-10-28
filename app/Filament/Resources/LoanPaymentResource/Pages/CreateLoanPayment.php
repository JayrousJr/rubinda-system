<?php

namespace App\Filament\Resources\LoanPaymentResource\Pages;

use App\Filament\Resources\LoanPaymentResource;
use App\Models\Debt;
use App\Models\FinancialData;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLoanPayment extends CreateRecord
{
    protected static string $resource = LoanPaymentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {

        $record = static::getModel()::create($data);
        $funds = FinancialData::findOrFail(1);
        $debt = Debt::findOrFail($record->debt_id);
        $debt->update(["remaining_debt" => $debt->remaining_debt - $record->amount_paid]);

        if ($debt->remaining_debt == 0) {
            $debt->update(["status" => "Paid"]);
        }

        $newTotal = $funds->total_amount + $record->amount_paid;
        $funds->update(["total_amount" => $newTotal]);

        // dd($debt->remaining_debt, $funds->total_amount);
        return $record;
    }
}