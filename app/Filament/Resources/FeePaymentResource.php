<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeePaymentResource\Pages;
use App\Filament\Resources\FeePaymentResource\RelationManagers;
use App\Models\Fee;
use App\Models\FeePayment;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FeePaymentResource extends Resource
{
    protected static ?string $model = FeePayment::class;

    protected static ?string $navigationGroup = 'Fees Management';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label("Fee Paid By")
                    ->required()
                    ->searchable()
                    ->live()
                    ->preload()
                    ->options(User::all()->pluck("name", "id"))
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (blank($state))
                            return;
                        $data = User::findOrFail($state);
                        $set('amount', $data->fee);
                    }),
                TextInput::make('amount')
                    ->required()
                    ->readOnly(),
                Select::make('fee_id')
                    ->label("Monthly Fee ID")
                    ->searchingMessage("Searching fee to pay ...")
                    ->searchPrompt("Searching ...")
                    ->searchable()->preload()
                    ->options(
                        function ($get) {
                            $userId = $get('user_id');
                            $registeredFees = FeePayment::where('user_id', $userId)->pluck('fee_id', 'id')->toArray();

                            return Fee::whereNotIn('id', $registeredFees)->pluck('name', 'id');
                        }
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('userFeePayment.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('feePayment.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money("Tsh", "0", "EU")
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
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
            ])->defaultSort('created_at', 'desc')
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
            'index' => Pages\ListFeePayments::route('/'),
            'create' => Pages\CreateFeePayment::route('/create'),
            'view' => Pages\ViewFeePayment::route('/{record}'),
            'edit' => Pages\EditFeePayment::route('/{record}/edit'),
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