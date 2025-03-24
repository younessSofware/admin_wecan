<?php

namespace App\Filament\Resources\DoctorResource\Pages;

use App\Filament\Resources\DoctorResource;
use App\Models\HospitalUserAttachment;
use Filament\Actions;
use Filament\Actions\Action;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Filament\Resources\Pages\CreateRecord;

class CreateDoctor extends CreateRecord
{
    protected static string $resource = DoctorResource::class;

    // protected function getFormActions(): array
    // {
    //     return [
    //         $this->getCreateFormAction(),
    //         $this->getCancelFormAction(),
    //     ];
    // }

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     $filePath = storage_path('app/file.txt');
    //     $content = json_encode($this->data);
    //     File::append($filePath, $content);
    //     return $this->data;
    // }



    // protected function beginDatabaseTransaction(): void
    // {
    //     $email = $this->data['email'];
    //     $foundedUser = User::where('email', $email)->first();
    //     if ($foundedUser) {
    //         $this->data['user_id'] = $foundedUser->id;
    //         unset($this->data['email']);
    //         $foundedUser->hospital_id = $this->data['hospital_id'];
    //         $foundedUser->save();
    //     }
    // }
}
