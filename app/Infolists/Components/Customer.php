<?php

namespace App\Infolists\Components;

use Filament\Infolists\Components\Component;

class Customer extends Component
{
    protected string $view = 'infolists.components.customer';

    public static function make(): static
    {
        return app(static::class);
    }
}
