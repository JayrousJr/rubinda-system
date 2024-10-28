<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Exports\UsersExport;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Actions\Action;
class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('export_users')
                ->label("Export Users")
                ->action(function () {
                    $fileName = "All_Users_" . now()->format('Y_m_d') . ".xlsx";
                    return Excel::download(new UsersExport, $fileName);
                })
                ->requiresConfirmation()
                ->icon('heroicon-o-arrow-down-tray'),
        ];
    }
}