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
            ->label('Явсан ачаа бүртгэх')

        ];
    }
        
public function getTabs(): array
{
    return [
        'all' => Tab::make('Бүгд')
              ->badge(Customer::query()
                ->count()
            ),
        'not_come' => Tab::make('Хүргэгдээгүй')
        ->modifyQueryUsing(fn (Builder $query) =>
        $query->where(function ($query) {
            $query->where('shipping_type', 'not_come')
                  ->orWhereNull('shipping_type');
        })
        )
        ->badge(
            Customer::query()
            ->where(function ($query) {
                $query->where('shipping_type', 'not_come')
                      ->orWhereNull('shipping_type');
            })
            ->count()
    ),
        'come' => Tab::make('Хүргэгдсэн')
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('shipping_type', 'come')
            )
             ->badge(Customer::query()
                ->where('shipping_type', 'come')
                ->count()
             )
    ];
}

}