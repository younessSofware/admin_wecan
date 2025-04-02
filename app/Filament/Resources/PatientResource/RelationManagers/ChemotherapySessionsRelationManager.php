<?php

namespace App\Filament\Resources\PatientResource\RelationManagers;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ChemotherapySessionsRelationManager extends RelationManager
{
    protected static string $relationship = 'chemotherapySessions';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->account_type === 'patient';
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('dashboard.chemotherapy_session');
    }
    public static function getModelLabel(): string
    {
        return __('dashboard.chemotherapy_session');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dashboard.chemotherapy_session');
    }

    public static function getHospitalId()
    {
        $currentUser = User::find(auth()->user()->id);
        return $currentUser->hospital_id;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('session_number')
                    ->label(__('dashboard.session_number'))
                    ->required()
                    ->maxLength(255),
                DateTimePicker::make('session_datetime')
                    ->required()
                    ->label(__('dashboard.datetime')),
                Textarea::make('instructions')
                    ->rows(5)
                    ->columnSpan(2)
                    ->label(__('dashboard.instructions')),
                Textarea::make('notes')
                    ->rows(5)
                    ->columnSpan(2)
                    ->label(__('dashboard.notes')),
                Checkbox::make('show')
                    ->label(__('dashboard.show_to_patient')),
                Forms\Components\TextInput::make('hospital_id')
                    ->default(self::getHospitalId())
                    ->extraAttributes(['style' => 'display: none;'])
                    ->hiddenLabel(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('session_number')
            ->columns([
                Tables\Columns\TextColumn::make('session_number')
                    ->label(__('dashboard.session_number')),
                Tables\Columns\TextColumn::make('session_datetime')
                    ->label(__('dashboard.datetime')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make() // This will add the create button
                    ->label(__('dashboard.add_chemotherapy_session')) // You can customize the label here
                    ->icon('heroicon-o-plus') // Optional: add an icon for the button
            ])
            ->actions([
                // This array will conditionally include actions based on hospital_id
                Tables\Actions\EditAction::make()->hidden(fn($record) =>
                $record->hospital_id != self::getHospitalId()),
                Tables\Actions\DeleteAction::make()->hidden(fn($record) =>
                $record->hospital_id != self::getHospitalId()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
