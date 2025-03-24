<?php

namespace App\Filament\Resources\NewConnectionRequestResource\Pages;

use App\Filament\Resources\NewConnectionRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class NewConnectionRequestList extends ListRecords
{
    protected static string $resource = NewConnectionRequestResource::class;

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCancelFormAction(),
        ];
    }
}
