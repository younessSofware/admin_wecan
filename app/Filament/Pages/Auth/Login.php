<?php

namespace App\Filament\Pages\Auth;

use Illuminate\Support\Facades\File;

use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    // Override methods and properties as needed

    protected function getLayoutData(): array
    {
        $data = parent::getLayoutData();
        $filePath = storage_path('app/file.txt');
        $content = json_encode([
            'tester' => '$record',
        ]);
        File::append($filePath, $content);
        // Replace the classes
        $data['wrapperClass'] = str_replace(
            'sm:max-w-lg',
            'max-w-lg',
            $data['wrapperClass'] ?? ''
        );

        return $data;
    }
}
