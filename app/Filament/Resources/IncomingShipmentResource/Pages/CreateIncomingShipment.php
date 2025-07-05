<?php

namespace App\Filament\Resources\IncomingShipmentResource\Pages;

use App\Filament\Resources\IncomingShipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateIncomingShipment extends CreateRecord
{
    protected static string $resource = IncomingShipmentResource::class;
    protected static bool $canCreateAnother = false;
    
    protected function getRedirectUrl(): string
    {
    return $this->getResource()::getUrl('index');
    }
}