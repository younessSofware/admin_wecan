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


    private static function getAttached()
    {

        $hospital_id = User::find(auth()->user()->id)->hospital->id ?? 29;
        // Assuming these return collections
        return HospitalUserAttachment::where('hospital_id', $hospital_id);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->tap(function ($table) {
                $table->query(self::getAttached());  // Correctly pass the query result
            })
            ->columns([
                TextColumn::make('sender.name')
                    ->label(__('dashboard.name'))
                    ->sortable(),
                TextColumn::make('sender.email')
                    ->label(__('dashboard.email'))
                    ->sortable(),
                TextColumn::make('sender.country.name_ar')
                    ->label(__('dashboard.country')),
                TextColumn::make('sender.account_type')
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
                            ->required(), // Ensure the field is required
                    ])
                    ->modalButton(__('dashboard.save')) // Button label in the modal
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
