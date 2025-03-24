<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Filament\Resources\PatientResource\RelationManagers;
use App\Models\Country;
use App\Models\User;
use App\Models\Hospital;
use App\Models\HospitalUserAttachment;
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

    public static function canCreate(): bool
    {
        return false;
    }




    public static function table(Table $table): Table
    {
        return $table->query(
            User::where('account_type', 'patient')
                ->join('hospital_user_attachments', function ($join) {
                    $join->on('users.hospital_id', '=', 'hospital_user_attachments.hospital_id')
                        ->whereColumn('users.id', 'hospital_user_attachments.user_id'); // Ensures the join on both user_id and hospital_id
                })
                ->where('hospital_user_attachments.hospital_id', self::getHospitalId())
                ->where('hospital_user_attachments.status', 'approved')
        )
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('user_id')
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('dashboard.name'))
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('dashboard.email'))
                    ->sortable(),
                TextColumn::make('country.name_ar')
                    ->label(__('dashboard.country')),
            ])
            ->actions(actions: [
                Tables\Actions\DeleteAction::make('cancel')
                    ->label(__('dashboard.unlink'))
                    ->modalHeading(__(key: 'dashboard.unlink_doctor'))
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        HospitalUserAttachment::where('user_id', $record->id)
                            ->where('hospital_id', $record->hospital_id)
                            ->delete();
                        User::find($record->id)->update(['hospital_id' => NULL]);
                    })
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
            // 'create' => Pages\CreatePatient::route('/create'),
            // 'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }
}
