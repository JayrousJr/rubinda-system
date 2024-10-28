<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanResource\Pages;
use App\Filament\Resources\LoanResource\RelationManagers;
use App\Models\Loan;
use App\Models\LoanApplication;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;
    protected static ?int $navigationSort = 1;

    // public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::where("status", "Pending")->count();
    // }
    protected static ?string $navigationGroup = 'Loans Management';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('loan_application_id')
                    ->label("Loan Application")
                    ->searchable()
                    ->hidden(fn(string $operation): bool => $operation === 'edit')
                    ->preload()
                    ->live()
                    ->options(function () {
                        return DB::table('loan_applications')
                            ->leftJoin('loans', 'loan_applications.id', '=', 'loans.loan_application_id')
                            ->select('loan_applications.id', DB::raw("loan_applications.name AS display"))
                            ->whereNull('loans.loan_application_id')
                            ->get()
                            ->pluck('display', 'id');
                    })
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (blank($state))
                            return;
                        $data = LoanApplication::findOrFail($state);
                        $set('duration', $data->duration);
                        $set('amount', $data->amount);
                        $set('total_amount_to_be_paid', $data->total_amount_to_be_paid);
                    }),
                TextInput::make('amount')
                    ->required()->readOnly()
                    ->maxLength(255),
                TextInput::make('total_amount_to_be_paid')
                    ->required()
                    ->readOnly()
                    ->maxLength(255),
                Hidden::make('duration')
                    ->required(),
                Select::make('status')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->options([
                        "Approved" => "Approved",
                        "Rejected" => "Rejected",
                    ]),
                Select::make("maelezo")
                    ->searchable()
                    ->required()
                    ->preload()
                    ->options([
                        "Mkopo Ume kubaliwa" => "Mkopo Ume kubaliwa",
                        'Kiasi unacho omba kinazidi kiwango cha mkopo uliopo' => 'Kiasi unacho omba kinazidi kiwango cha mkopo uliopo',
                        'Huduma ya kutoa mkopo ime sitishwa kwasasa' => 'Huduma ya kutoa mkopo ime sitishwa kwasasa',
                        'Huruhusiwi kukopa kabla ya kulipa deni lako la nyuma' => 'Huruhusiwi kukopa kabla ya kulipa deni lako la nyuma',
                        'Hauna vigezo vya kupewa mkopo kwa sasa' => 'Hauna vigezo vya kupewa mkopo kwa sasa',
                    ]),
                Hidden::make("user_id")->default(Auth::user()->id)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('appliedLoan.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('duration')
                    ->description("Month(s)")
                    ->searchable(),
                TextColumn::make('amount')
                    ->searchable()
                    ->money('Tsh'),
                TextColumn::make('total_amount_to_be_paid')
                    ->searchable()
                    ->money('Tsh'),
                TextColumn::make('status')
                    ->badge()
                    ->color(function (string $state) {
                        if ($state == 'Approved') {
                            return 'primary';
                        } else {
                            return 'danger';
                        }
                    })
                    ->searchable(),
                TextColumn::make('userLoan.name')
                    ->label("Created By"),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListLoans::route('/'),
            'create' => Pages\CreateLoan::route('/create'),
            'view' => Pages\ViewLoan::route('/{record}'),
            'edit' => Pages\EditLoan::route('/{record}/edit'),
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