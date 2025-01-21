<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupporterResource\Pages;
use App\Filament\Resources\SupporterResource\RelationManagers;
use App\Models\Supporter;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupporterResource extends Resource
{
    protected static ?string $model = Supporter::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?int $navigationSort = 6;

    public static function getNavigationLabel(): string
    {
        return __('dashboard.supporters');
    }

    public static function getModelLabel(): string
    {
        return __('dashboard.supporter');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dashboard.supporters');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name_ar')
                    ->required()
                    ->maxLength(255)
                    ->label(__('dashboard.name_ar')),
                Forms\Components\TextInput::make('name_en')
                    ->required()
                    ->maxLength(255)
                    ->label(__('dashboard.name_en')),
                Textarea::make('description')
                    ->rows(5)
                    ->label(__('dashboard.description')),
                FileUpload::make('image')
                    ->label(__('dashboard.image'))
                    ->visibility('public')->image()
                    ->imageEditor()
                    ->maxSize(2048)
                    ->required(),
                Toggle::make('visible')
                    ->label(__('dashboard.visible'))

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->label(__('dashboard.image')),
                TextColumn::make('name_ar')->label(__('dashboard.name_ar')),
                TextColumn::make('name_en')->label(__('dashboard.name_en')),
                ToggleColumn::make('visible')->label(__('dashboard.visible'))
            ])
            ->filters([
                //
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupporters::route('/'),
            'create' => Pages\CreateSupporter::route('/create'),
            'edit' => Pages\EditSupporter::route('/{record}/edit'),
        ];
    }
}
