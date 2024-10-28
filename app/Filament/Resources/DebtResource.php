<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DebtResource\Pages;
use App\Filament\Resources\DebtResource\RelationManagers;
use App\Models\Debt;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DebtResource extends Resource
{
    protected static ?string $model = Debt::class;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where("status", "Active")->count();
    }
    protected static ?string $navigationGroup = 'Debts and Expenses';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('loan_id')
                    ->readOnly(fn(string $operation): bool => $operation === 'edit')
                    ->hidden(fn(string $operation): bool => $operation === 'edit')
                    ->numeric(),
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->hidden(fn(string $operation): bool => $operation === 'edit')
                    ->numeric(),
                Forms\Components\TextInput::make('original_amount')
                    ->label("Original Loan")
                    ->required()
                    ->readOnly()
                    ->maxLength(255),
                Forms\Components\TextInput::make('total_debt')
                    ->required()
                    ->readOnly()
                    ->maxLength(255),
                Forms\Components\TextInput::make('remaining_debt')
                    ->required()->readOnly()
                    ->maxLength(255),
                Forms\Components\TextInput::make('status')
                    ->required()->readOnly()
                    ->maxLength(255)
                    ->default('active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('loanDebt.appliedLoan.name')
                    ->label("Loan ID"),
                TextColumn::make('original_amount')
                    ->money('Tsh')
                    ->label("Original Loan")
                    ->searchable(),
                TextColumn::make('total_debt')
                    ->money('Tsh')
                    ->searchable(),
                TextColumn::make('remaining_debt')
                    ->money('Tsh')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(function (string $state) {
                        if ($state == 'Paid') {
                            return 'primary';
                        } else {
                            return 'danger';
                        }
                    })
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label("Debt Reg Date")
                    ->date()
                    ->sortable()
                // ->toggleable(isToggledHiddenByDefault: true)
                ,
                TextColumn::make('overdue_date')
                    ->label('Overdue Date')
                    ->sortable()
                    ->date(),
                TextColumn::make("days_remaining")
                    ->badge()
                    ->label("Days Overdue")
                    ->getStateUsing(fn($record) => $record->days_remaining)
                    ->color(function ($record) {
                        $state = $record->days_remaining;
                        if ($state < 0)
                            return "danger";
                        else if ($state <= 10)
                            return "warning";
                        else
                            return "success";
                    }),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDebts::route('/'),
            'create' => Pages\CreateDebt::route('/create'),
            'view' => Pages\ViewDebt::route('/{record}'),
            'edit' => Pages\EditDebt::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}