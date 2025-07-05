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
            ->where('logistic_type', 'incoming'); // ✅ ここで絞る
    }

public function getTabs(): array
{
    return [
        'all' => Tab::make('Бүгд')
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('logistic_type', 'incoming')
            ),

        'not_pay' => Tab::make('Төлөгдөөгүй')
    ->modifyQueryUsing(fn (Builder $query) =>
        $query->where('payment_status', 'not_payd')
    )
    ->badge(fn () => Customer::query()
        ->where('payment_status', 'not_payd')
        ->count()
    ),

        'payd' => Tab::make('Төлсөн')
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('payment_status', 'payd')
                      ->where('logistic_type', 'incoming')
            ),

        'card' => Tab::make('Карт')
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('payment_type', '1')
                      ->where('logistic_type', 'incoming')
            ),

        'cash' => Tab::make('Бэлэн')
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('payment_type', '0')
                      ->where('logistic_type', 'incoming')
            ),
    ];
}
}