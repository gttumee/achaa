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
    
       protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->where('logistic_type', 'incoming');
    }

public function getTabs(): array
{
 return [
        'all' => Tab::make('Бүгд')
            ->badgeColor('info')
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('logistic_type', 'incoming')
            )
              ->badge(Customer::query()
                ->where('logistic_type', 'incoming')
                ->count()
            ),


      'not_pay' => Tab::make('Төлөгдөөгүй')
      ->badgeColor('info')
        ->modifyQueryUsing(fn (Builder $query) =>
        $query->where(function ($query) {
            $query->where('payment_status', 'not_payd')
                  ->orWhereNull('payment_status');
        })->where('logistic_type', 'incoming')
        )
        ->badge(
            Customer::query()
            ->where(function ($query) {
                $query->where('payment_status', 'not_payd')
                      ->orWhereNull('payment_status');
            })
            ->where('logistic_type', 'incoming')
            ->count()
    ),

        'payd' => Tab::make('Төлсөн')
            ->badgeColor('info')
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('payment_status', 'payd')
                      ->where('logistic_type', 'incoming')
            )
             ->badge(Customer::query()
                ->where('payment_status', 'payd')
                ->where('logistic_type', 'incoming')
                ->count()
             )
    ];
}
}