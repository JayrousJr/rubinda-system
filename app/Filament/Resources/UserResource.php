<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Role;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'System Management';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Select::make('fee')
                    ->required()
                    ->visible(Auth::user()->isBoth())
                    ->searchable()
                    ->preload()
                    ->options([
                        "5000" => "5,000",
                        "10000" => "10,000",
                    ]),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique()
                    ->maxLength(255),
                TextInput::make('password')
                    ->password()
                    ->default("password")
                    ->dehydrateStateUsing(fn(string $state): string => Hash::make($state))
                    ->dehydrated(fn(?string $state): bool => filled($state))
                    ->required(fn(string $operation): bool => $operation === 'create'),
                Select::make('roles')
                    ->label('Role')
                    ->visible(Auth::user()->isBoth())
                    ->optionsLimit(4)
                    ->searchable()
                    ->live()
                    ->relationship('roles', 'name')
                    ->preload()
                    ->afterStateUpdated(function ($state, Set $set) {
                        if (blank($state))
                            return;
                        $role = Role::find($state);
                        $set('role', $role->name);
                    }),
                TextInput::make('role')
                    ->required(fn(string $operation): bool => $operation === 'create')
                    ->readOnly()
                    ->visible(Auth::user()->isBoth())
                    ->helperText('This is automatically updated')
                    ->label('Role In the System'),
                FileUpload::make('profile_photo_path')
                    ->label("Profile")
                    ->default('img/profile-photos/default.jpg')
                    ->image()
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1')
                    ->imageEditor()
                    ->imageResizeTargetWidth('500')
                    ->imageResizeTargetHeight('500')
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, callable $get): string {
                        $name = $get('email');
                        return (string) str('img/profile-photos/profile_' . $name . '.webp');
                    })->label('Profile Image'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(function (string $state) {
                        if ($state == 'Secretary') {
                            return 'primary';
                        } else if ($state == 'Accountant') {
                            return 'info';
                        } else {
                            return 'warning';
                        }
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('fee')
                    ->money('Tsh')
                    ->badge()
                    ->color(function (string $state) {
                        if ($state == '5000')
                            return 'slate';
                        else
                            return 'gray';
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('profile_photo_path')
                    ->searchable(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        if (Auth::user()->isBoth()) {
            return $query;
        }
        if (Auth::user()->isMember()) {
            return $query->where("id", Auth::user()->id);
        }
        return $query->where('id', null);
        ;
    }
}