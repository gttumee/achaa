<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;
    protected static bool $canCreateAnother = false;
    
        protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}
public static function getCreateFormSuccessNotification(): Notification
{
    return Notification::make()
        ->title('Амжилттай бүртгэгдлээ')
        ->success();
}

}