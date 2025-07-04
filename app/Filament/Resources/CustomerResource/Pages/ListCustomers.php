<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Шинээр ачаа бүртгэх')

        ];
    }
public function getTabs(): array
{
    return [
         'all' => Tab::make('Бүгд'),
        'not_pay' => Tab::make('Төлөгдөөгүй')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('payment_status', 'not_payd'))
             ->badge(Customer::query()->where('payment_status', 'not_payd')->count()),
        'payd' => Tab::make('Төлсөн')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('payment_status', 'payd')),
        'card' => Tab::make('Карт')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('payment_type', '1')),
        'cash' => Tab::make('Бэлэн')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('payment_type', '0')),
    ];
}
}