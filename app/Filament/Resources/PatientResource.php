<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Filament\Resources\PatientResource\RelationManagers;
use App\Models\Country;
use App\Models\User;
use App\Models\Hospital;

use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PatientResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 4;
    public static function create($request)
    {
        abort(403); // Prevents access to the "Add" page
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard.patients');
    }




    public static function getPluralModelLabel(): string
    {
        return __('dashboard.patients');
    }

    public static function getModelLabel(): string
    {
        return __('dashboard.patient');
    }

    public static function getHospitalId()
    {
        $currentUser = User::find(auth()->user()->id);
        return $currentUser->hospital_id;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('dashboard.name'))
                            ->maxLength(255)
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label(__('dashboard.email'))
                            ->required()
                            ->email()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Select::make('country_id')
                            ->label(__('dashboard.country'))
                            ->options(Country::all()->pluck('name_ar', 'id'))
                            ->searchable(),
                        Select::make('account_status')
                            ->label(__('dashboard.account_status'))
                            ->options([
                                'active' => __('dashboard.active'),
                                'cancelled' => __('dashboard.cancelled'),
                                'banned' => __('dashboard.banned'),
                            ])
                            ->searchable(),

                        Forms\Components\TextInput::make('password')
                            ->type('password')
                            ->label(__('dashboard.password'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('contact_number')
                            ->type('tel')
                            ->label(__('dashboard.contact_number'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('hospital_id')
                            ->default(self::getHospitalId())
                            ->extraAttributes(['style' => 'display: none;'])
                            ->hiddenLabel(),

                        Forms\Components\TextInput::make('account_type')
                            ->default('patient')
                            ->extraAttributes(['style' => 'display: none;'])
                            ->hiddenLabel(),
                    ])
                    ->columns(2)
                    ->columnSpan(['lg' => fn(?User $record) => $record === null ? 3 : 2]),

            ])
            ->columns(2);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->query(
                User::where('account_type', 'patient')
                    ->where('hospital_id', self::getHospitalId())
            )

            ->columns([
                TextColumn::make('name')
                    ->label(__('dashboard.name'))
                    ->searchable(isIndividual: true)
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('dashboard.email'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable(),
                TextColumn::make('country.name_ar')
                    ->label(__('dashboard.country')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function getRelations(): array
    {
        $relations = [];

        return $relations;
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }
}
