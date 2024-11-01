<?php

namespace App\Filament\Resources\LoanResource\Pages;

use App\Filament\Resources\LoanResource;
use App\Models\Debt;
use App\Models\FinancialData;
use App\Models\LoanApplication;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditLoan extends EditRecord
{
    protected static string $resource = LoanResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            // Actions\DeleteAction::make(),
            // Actions\ForceDeleteAction::make(),
            // Actions\RestoreAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        $userID = $record->appliedLoan->loanRequest->id;
        $prevDebt = Debt::where('loan_id', $record->id);
        $exists = Debt::where('loan_id', $record->id)->exists();
        $funds = FinancialData::findOrFail(1);


        if ($data["status"] == "Approved") {
            // if the loan is Approved and there is no a debt of that loan, create a debt and reduce the group funds as well as make the loanapplication to be approved else if the debt exists then remove that debt
            if (!$exists) {
                $debt = new Debt();
                $debt->loan_id = $record->id;
                $debt->user_id = $userID;
                $debt->total_debt = $data["total_amount_to_be_paid"];
                $debt->original_amount = $data["amount"];
                $debt->remaining_debt = $data["total_amount_to_be_paid"];
                $debt->status = "Active";
                $debt->save();
                $loanApplication = LoanApplication::findOrFail($record->loan_application_id);
                $loanApplication->update([
                    "status" => $record->status,
                    "maelezo" => $data["maelezo"],
                ]);
                $newTotal = $funds->total_amount - $data["amount"];
                $funds->update(["total_amount" => $newTotal]);
            } else {
                $prevDebt->delete();
            }
        } else if ($data["status"] == "Rejected") {
            // if the loan is rejected and there is a debt of that loan, remove the debt and make the increment the funds and update the statuses
            if ($exists) {
                $prevDebt->delete();
                $newTotal = $funds->total_amount + $data["amount"];
                $funds->update(["total_amount" => $newTotal]);
                $loanApplication = LoanApplication::findOrFail($record->loan_application_id);
                $loanApplication->update([
                    "status" => $record->status,
                    "maelezo" => $data["maelezo"],
                ]);
            } else {
                $loanApplication = LoanApplication::findOrFail($record->loan_application_id);
                $loanApplication->update([
                    "status" => $record->status,
                    "maelezo" => $data["maelezo"],
                ]);
            }
        } else {
            $debt = Debt::where('loan_id', $record->id)->first();
            if ($debt) {
                $debt->delete();

            }
        }
        return $record;
    }
}