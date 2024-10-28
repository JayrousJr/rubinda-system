<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanPaymentResource\Pages;
use App\Filament\Resources\LoanPaymentResource\RelationManagers;
use App\Models\Debt;
use App\Models\LoanPayment;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class LoanPaymentResource extends Resource
{
    protected static ?string $model = LoanPayment::class;
    protected static ?string $navigationGroup = 'Loans Management';
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('debt_id')
                    ->label("Debt To Pay")
                    ->required()
                    ->preload()
                    ->searchable()
                    ->options(Debt::where("status", "Active")
                        ->where("remaining_debt", "!=", 0)
                        ->get()->pluck("loanDebt.appliedLoan.name", "id"))
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (blank($state))
                            return;
                        $data = Debt::findOrFail($state);
                        $set("currentDebt", $data->remaining_debt);
                    }),
                TextInput::make("currentDebt")->label("Current Debt")
                    ->readOnly()
                    ->hidden(fn(string $operation): bool => $operation === 'edit')
                    ->hidden(fn(string $operation): bool => $operation === 'view'),
                TextInput::make('amount_paid')
                    ->required()
                    ->label("Amount")
                    ->lte("currentDebt")
                    ->maxLength(255),
                DatePicker::make('date')
                    ->required(),
                Hidden::make("user_id")->default(Auth::user()->id)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('debtPayment.loanDebt.appliedLoan.name')
                    ->label("Loan Identity")
                    ->searchable(),
                TextColumn::make('amount_paid')
                    ->money('Tsh')
                    ->searchable(),
                TextColumn::make('date')
                    ->label("Payment Made on")
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label("Registred")
                    ->dateTime()
                    ->since()
                    ->sortable(),
                TextColumn::make('userLoanPayment.name')
                    ->label("Created By"),
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
            'index' => Pages\ListLoanPayments::route('/'),
            'create' => Pages\CreateLoanPayment::route('/create'),
            'view' => Pages\ViewLoanPayment::route('/{record}'),
            'edit' => Pages\EditLoanPayment::route('/{record}/edit'),
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