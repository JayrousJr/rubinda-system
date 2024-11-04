<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanApplicationResource\Pages;
use App\Filament\Resources\LoanApplicationResource\RelationManagers;
use App\Models\FinancialData;
use App\Models\LoanApplication;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ButtonAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

use function App\Helpers\funds;

class LoanApplicationResource extends Resource
{
    protected static ?string $model = LoanApplication::class;
    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where("status", "Pending")->count();
    }
    protected static ?string $navigationGroup = 'Loans Management';
    protected static ?string $navigationIcon = 'heroicon-s-clipboard-document-list';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('user_id')
                    ->label("My Account Id")
                    ->default(Auth::user()->id)
                    ->readOnly()
                    ->numeric(),
                TextInput::make('duration')->default(3)->readOnly(),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->label("Loan Amount")
                    ->live(debounce: 500)
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        $name = Auth::user()->name . ' ' . date("j-M-Y");
                        $amount = floatval($get('amount'));
                        $interest = floatval(FinancialData::first()->interest_rate);
                        $calculated_Return = round(floatval($amount * (($interest / 100) + 1) * 3), 0);
                        $set("total_amount_to_be_paid", $calculated_Return);
                        $set("name", $name);
                    }),
                TextInput::make('total_amount_to_be_paid')
                    ->required()
                    ->readOnly()
                    ->maxLength(255),
                TextInput::make('status')
                    ->required()
                    ->readOnly()
                    ->default("Pending")
                    ->maxLength(255),
                TextInput::make('name')
                    ->readOnly(),
                Textarea::make('maelezo')
                    ->visible(fn(string $operation): bool => $operation === 'view')
                    ->readOnly(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('duration')
                    ->description("Months")
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount Borrowed')
                    ->money('Tsh'),
                Tables\Columns\TextColumn::make('total_amount_to_be_paid')
                    ->label('Total Amount to Return')
                    ->money('Tsh'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(function (string $state) {
                        if ($state == 'Pending') {
                            return 'warning';
                        } else if ($state == 'Approved') {
                            return 'primary';
                        } else {
                            return 'danger';
                        }
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
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
            'index' => Pages\ListLoanApplications::route('/'),
            'create' => Pages\CreateLoanApplication::route('/create'),
            'view' => Pages\ViewLoanApplication::route('/{record}'),
            'edit' => Pages\EditLoanApplication::route('/{record}/edit'),
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