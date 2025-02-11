<?php

// app/Filament/Resources/ChatResource.php
namespace App\Filament\Resources;

use App\Filament\Resources\ChatResource\Pages;
use App\Models\Chat;
use App\Models\ChatMessage;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Tables\Table;

class ChatResource extends Resource
{
    protected static ?string $model = ChatMessage::class;
    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    public static function getModelLabel(): string
    {
        return __('dashboard.chats');
    }


    public static function getNavigationLabel(): string
    {
        return __('dashboard.chats');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('user_id')
                ->label('User ID')
                ->required(),
            Textarea::make('message')
                ->label('Message')
                ->required(),
        ]);
    }
    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('user.name')->label('User'),
            TextColumn::make('message')->label('Message'),
            TextColumn::make('created_at')->label('Sent At'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChats::route('/'),
            'create' => Pages\CreateChat::route('/create'),
            // 'edit' => Pages\EditChat::route('/{record}/edit'),
        ];
    }
}
