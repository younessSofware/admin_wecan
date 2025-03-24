<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewConnectionRequestResource\Pages;
use App\Filament\Resources\NewConnectionRequestResource\RelationManagers;
use App\Models\Country;
use App\Models\HospitalUserAttachment;
use App\Models\User;
use App\Models\Hospital;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\File;

class NewConnectionRequestResource extends Resource
{
    protected static ?string $model = HospitalUserAttachment::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 4;
    public static function create($request)
    {
        abort(403); // Prevents access to the "Add" page
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard.new_connection_requests');
    }




    public static function getPluralModelLabel(): string
    {
        return __('dashboard.new_connection_requests');
    }

    public static function getHospitalId()
    {
        $currentUser = User::find(auth()->user()->id);
        return $currentUser->hospital_id;
    }





    public static function table(Table $table): Table
    {
        return $table->query(
            User::where('account_type', 'doctor')
                ->orWhere('account_type', 'patient')
                ->join('hospital_user_attachments', function ($join) {
                    $join->on('users.hospital_id', '=', 'hospital_user_attachments.hospital_id')
                        ->whereColumn('users.id', 'hospital_user_attachments.user_id'); // Ensures the join on both user_id and hospital_id
                })
                ->where('hospital_user_attachments.hospital_id', self::getHospitalId())
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
                TextColumn::make('account_type')
                    ->label(__('dashboard.account_type'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'patient' => 'warning',
                        'doctor' => 'info',
                    }),
                TextColumn::make('status')
                    ->label(__('dashboard.status'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => __('dashboard.pending'),
                        'approved' => __('dashboard.approved'),
                        'rejected' => __('dashboard.rejected'),
                    })
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading(__('dashboard.edit_status')) // Modal title
                    ->form([
                        Select::make('status')
                            ->options([
                                'pending' => __('dashboard.pending'),
                                'approved' => __('dashboard.approved'),
                                'rejected' => __('dashboard.rejected'),
                            ])
                            ->required(),
                    ])
                    ->modalButton(__('dashboard.save'))
                    ->action(function ($record, array $data) {
                        $filePath = storage_path('app/file.txt');
                        $content = json_encode($data);
                        File::append($filePath, $content);
                        // still record give me the first row id
                        // HospitalUserAttachment::find($record->id)
                        //     ->update(['status' => $data['status']]);
                    }),
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
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        $relations = [];

        return $relations;
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\NewConnectionRequestList::route('/'),

            // 'create' => Pages\CreateDoctor::route('/create'),
            // 'edit' => Pages\EditDoctor::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        // return ['name', 'email'];
        return [];
    }
}
