<?php

namespace App\Filament\Resources\PatientResource\RelationManagers;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PatientNotesRelationManager extends RelationManager
{
    protected static string $relationship = 'patientNotes';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->account_type === 'patient';
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('dashboard.patient_note');
    }
    public static function getModelLabel(): string
    {
        return __('dashboard.patient_note');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dashboard.patient_note');
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
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->label(__('dashboard.title'))
                    ->maxLength(255),
                DateTimePicker::make('datetime')
                    ->required()
                    ->label(__('dashboard.datetime')),
                FileUpload::make('attachments')
                    ->label(__('dashboard.attachments'))
                    ->visibility('public')
                    ->multiple(),
                Textarea::make('notes')
                    ->rows(5)
                    ->columnSpan(2)
                    ->label(__('dashboard.notes')),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')->label(__('dashboard.title')),
                Tables\Columns\TextColumn::make('datetime')->label(__('dashboard.datetime')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make() // This will add the create button
                    ->label(__('dashboard.add_patient_notes')) // You can customize the label here
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
