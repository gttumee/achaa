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
    
     protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->where('logistic_type', 'outgoing'); // ✅ ここで絞る
    }
    
public function getTabs(): array
{
    return [
        'all' => Tab::make('Бүгд')
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('logistic_type', 'outgoing')
            )
              ->badge(Customer::query()
                ->where('logistic_type', 'outgoing')
                ->count()
            ),


        'not_pay' => Tab::make('Төлөгдөөгүй')
    ->modifyQueryUsing(fn (Builder $query) =>
        $query->where(function ($query) {
            $query->where('payment_status', 'not_payd')
                  ->orWhereNull('payment_status');
        })->where('logistic_type', 'outgoing')
    )
    ->badge(
        Customer::query()
            ->where(function ($query) {
                $query->where('payment_status', 'not_payd')
                      ->orWhereNull('payment_status');
            })
            ->where('logistic_type', 'outgoing')
            ->count()
    ),


        'payd' => Tab::make('Төлсөн')
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->where('payment_status', 'payd')
                      ->where('logistic_type', 'outgoing')
            )
             ->badge(Customer::query()
                ->where('payment_status', 'payd')
                ->where('logistic_type', 'outgoing')
                ->count()
             )
    ];
}

}