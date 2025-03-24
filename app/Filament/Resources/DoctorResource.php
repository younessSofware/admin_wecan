<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoctorResource\Pages;
use App\Filament\Resources\DoctorResource\RelationManagers;
use App\Models\Country;
use App\Models\HospitalUserAttachment;
use App\Models\User;
use App\Models\Hospital;
use Illuminate\Support\Facades\File;

use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
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

class DoctorResource extends Resource
{
    protected static ?string $model = HospitalUserAttachment::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 4;
    public static function create($request)
    {
        abort(403); // Prevents access to the "Add" page
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard.doctors');
    }


    public static function getHospitalId()
    {
        $currentUser = User::find(auth()->user()->id);
        return $currentUser->hospital_id;
    }

    public static function getTableQuery()
    {
        $query =  User::where('account_type', 'doctor')
            ->where('hospital_id', self::getHospitalId());
        dd($query->toSql());
        return  $query;
    }

    public static function getTableQuery2()
    {

        $query =  User::where('account_type', 'doctor')
            ->join('hospital_user_attachments', function ($join) {
                $join->on('users.hospital_id', '=', 'hospital_user_attachments.hospital_id')
                    ->whereColumn('users.id', 'hospital_user_attachments.user_id');
            })
            ->where(
                'hospital_user_attachments.hospital_id',
                self::getHospitalId()
            )
            ->where('hospital_user_attachments.status', 'approved');
        // dd($query->toSql());
        return  $query;
    }




    public static function getPluralModelLabel(): string
    {
        return __('dashboard.doctors');
    }


    public static function getModelLabel(): string
    {
        return __('dashboard.doctor');
    }
    public static function canCreate(): bool
    {
        return false;
    }


    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             Forms\Components\Section::make()
    //                 ->schema([
    //                     Forms\Components\TextInput::make('email')
    //                         ->label(__('dashboard.email'))
    //                         ->email()
    //                         ->maxLength(255)
    //                         ->unique(ignoreRecord: true),
    //                     Forms\Components\TextInput::make('hospital_id')
    //                         ->default(self::getHospitalId())
    //                         ->extraAttributes(['style' => 'display: none;'])
    //                         ->hiddenLabel(),
    //                     Forms\Components\TextInput::make('sender_id')
    //                         ->default(auth()->user()->id)
    //                         ->extraAttributes(['style' => 'display: none;'])
    //                         ->hiddenLabel(),
    //                     Forms\Components\TextInput::make('status')
    //                         ->default('pending')
    //                         ->extraAttributes(['style' => 'display: none;'])
    //                         ->hiddenLabel(),
    //                 ])
    //                 ->columns(2)
    //         ]);
    // }



    public static function table(Table $table): Table
    {
        return $table
            ->query(self::getTableQuery2())
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
                Tables\Columns\TextColumn::make('contact_number')
                    ->label(__('dashboard.phone_number'))
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\DeleteAction::make('cancel')
                    ->label(__('dashboard.unlink'))
                    ->modalHeading(__(key: 'dashboard.unlink_doctor'))
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        // HospitalUserAttachment::where('user_id', $record->id)
                        //     ->where('hospital_id', $record->hospital_id)
                        //     ->delete();
                        // User::find($record->id)->update(['hospital_id' => NULL]);
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
            'index' => Pages\ListDoctors::route('/'),
            // 'create' => Pages\CreateDoctor::route('/create'),
            // 'edit' => Pages\EditDoctor::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }
}
