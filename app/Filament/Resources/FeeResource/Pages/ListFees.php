<?php

namespace App\Filament\Resources\FeeResource\Pages;

use App\Filament\Resources\FeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Exports\FeeStatusExport;
use App\Models\Fee;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Maatwebsite\Excel\Facades\Excel;

class ListFees extends ListRecords
{
    protected static string $resource = FeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('export')
                ->label("Export Excel")
                ->action(function (array $data) {
                    // Retrieve the selected fee name based on the `fee_id`
                    $fee = Fee::find($data['fee_id']);
                    $feeName = $fee ? $fee->name : 'Fee_Payment';

                    // Generate a dynamic filename using the fee name and current date
                    $fileName = "{$feeName}_Payment_" . now()->format('Y_m_d') . ".xlsx";

                    // Download the file with the dynamic name
                    return Excel::download(new FeeStatusExport($data['fee_id']), $fileName);
                })
                ->requiresConfirmation()
                ->form([
                    Select::make('fee_id')
                        ->label('Select Fee')
                        ->options(Fee::all()->pluck('name', 'id'))
                        ->required()
                        ->searchable()
                ])
                ->icon('heroicon-o-arrow-down-tray'),
        ];
    }
}