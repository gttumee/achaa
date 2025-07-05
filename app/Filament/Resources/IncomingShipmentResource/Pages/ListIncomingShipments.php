<?php

namespace App\Filament\Resources\IncomingShipmentResource\Pages;

use App\Filament\Resources\IncomingShipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Customer;


class ListIncomingShipments extends ListRecords
{
    protected static string $resource = IncomingShipmentResource::class;

  protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Ирсэн ачаа бүртгэх')
            ->color('info')

        ];
    }
    
public function getTabs(): array
{
 return [
        'all' => Tab::make('Бүгд')
            ->badgeColor('info')
              ->badge(Customer::query()
                ->count()
            ),

      'not_pay' => Tab::make('Төлөгдөөгүй')
      ->badgeColor('info')
        ->modifyQueryUsing(fn (Builder $query) =>
        $query->where(function ($query) {
            $query->where('payment_status', 'not_payd')
                  ->orWhereNull('payment_status');
        })
        )
        ->badge(
            Customer::query()
            ->where(function ($query) {
                $query->where('payment_status', 'not_payd')
                      ->orWhereNull('payment_status');
            })
            ->count()
    ),

        'payd' => Tab::make('Төлөгдсөн')
            ->badgeColor('info')
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('payment_status', 'payd')
            )
             ->badge(Customer::query()
                ->where('payment_status', 'payd')
                ->count()
             )
    ];
}
}