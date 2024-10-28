<?php

namespace App\Filament\Resources\LoanResource\Pages;

use App\Filament\Resources\LoanResource;
use App\Models\Debt;
use App\Models\FinancialData;
use App\Models\LoanApplication;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

use function App\Helpers\funds;

class CreateLoan extends CreateRecord
{
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected static string $resource = LoanResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {

        $record = static::getModel()::create($data);
        $funds = FinancialData::findOrFail(1);
        $loanApplication = LoanApplication::findOrFail($record->loan_application_id);
        $debt = new Debt();


        if ($data["status"] == "Approved") {
            // Change the loan application status
            $loanApplication->update([
                "status" => $record->status,
                "maelezo" => $record->maelezo,
            ]);
            // update the debt
            $debt->loan_id = $record->id;
            $debt->user_id = 1;
            $debt->original_amount = $data["amount"];
            $debt->total_debt = $data["total_amount_to_be_paid"];
            $debt->remaining_debt = $data["total_amount_to_be_paid"];
            $debt->status = "Active";
            $debt->save();
            // Decrement the funds
            $newTotal = $funds->total_amount - $data["amount"];
            $funds->update(["total_amount" => $newTotal]);

        } else {
            // dd($data["maelezo"]);
            $loanApplication->update([
                "status" => $record->status,
                "maelezo" => $data["maelezo"],
            ]);
        }
        return $record;
    }
}