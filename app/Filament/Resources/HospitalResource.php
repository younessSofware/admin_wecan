<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HospitalResource\Pages;
use App\Models\Hospital;
use App\Models\Country;
use App\Models\Region;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;

class HospitalResource extends Resource
{
    protected static ?string $model = Hospital::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function form(Form $form): Form
    {
         return $form
            ->schema([
                Forms\Components\TextInput::make('hospital_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('hospital_logo')
                    ->image()
                    ->directory('hospital_logos')
                    ->preserveFilenames()
                    ->maxSize(2048)
                    ->visibility('public')
                    ->imageEditor(),
                Forms\Components\TextInput::make('user_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->minLength(8)
                    ->same('password_confirmation')
                    ->dehydrated(fn ($state) => filled($state))
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                Forms\Components\TextInput::make('password_confirmation')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->minLength(8)
                    ->dehydrated(false),
                Forms\Components\TextInput::make('contact_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('country_id')
                    ->label('Country')
                    ->options(Country::all()->pluck('name_ar', 'id'))
                    ->searchable()
                    ->required()
                    ->reactive(),
                Forms\Components\Select::make('city')
                    ->label('City')
                    ->options(function (callable $get) {
                        $country = Country::find($get('country_id'));
                        if (!$country) {
                            return Region::all()->pluck('name_ar', 'id');
                        }
                        return $country->regions->pluck('name_ar', 'id');
                    })
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->disabled(fn (callable $get) => !$get('country_id')),
                Forms\Components\Select::make('account_status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'banned' => 'Banned',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hospital_name')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('hospital_logo'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country.name_ar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('region.name_ar')
                    ->label('City')
                    ->searchable(),
                Tables\Columns\TextColumn::make('account_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'banned' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('activate')
                    ->action(fn (Hospital $record) => $record->activate())
                    ->requiresConfirmation()
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->visible(fn (Hospital $record): bool => $record->account_status === 'pending'),
                Tables\Actions\Action::make('ban')
                    ->action(fn (Hospital $record) => $record->ban())
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->visible(fn (Hospital $record): bool => $record->account_status !== 'banned'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->action(fn (Collection $records) => $records->each->activate())
                        ->requiresConfirmation()
                        ->color('success')
                        ->icon('heroicon-o-check'),
                    Tables\Actions\BulkAction::make('ban')
                        ->action(fn (Collection $records) => $records->each->ban())
                        ->requiresConfirmation()
                        ->color('danger')
                        ->icon('heroicon-o-x-mark'),
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
            'index' => Pages\ListHospitals::route('/'),
            'create' => Pages\CreateHospital::route('/create'),
            'edit' => Pages\EditHospital::route('/{record}/edit'),
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