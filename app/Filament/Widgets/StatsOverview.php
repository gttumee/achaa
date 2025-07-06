<?php

namespace App\Filament\Widgets;

use App\Infolists\Components\Customer;
use App\Models\Customer as ModelsCustomer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

class StatsOverview extends BaseWidget
{
    
    protected function getStats(): array
    {
   return [
    Stat::make('Нийт Ачаа', ModelsCustomer::query()->count())
        ->icon('heroicon-s-swatch'),

    Stat::make('Төлбөр төлөгдөөгүй', ModelsCustomer::query()
        ->where('payment_type', 'not_pay')
        ->count())
        ->color('success')
        ->icon('heroicon-o-x-circle'),

    Stat::make('Хүргэгдээгүй ачаа', ModelsCustomer::query()
        ->where(function ($query) {
            $query->where('shipping_type', 'not_come')
                  ->orWhereNull('shipping_type');
        })
        ->count())
        ->icon('heroicon-o-truck')
        ->color('success'),
];

    }
}