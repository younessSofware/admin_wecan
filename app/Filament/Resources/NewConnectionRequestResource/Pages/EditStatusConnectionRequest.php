<?php

namespace App\Filament\Resources\NewConnectionRequestResource\Pages;

use App\Filament\Resources\NewConnectionRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStatusConnectionRequest extends EditRecord
{
    protected static string $resource = NewConnectionRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
