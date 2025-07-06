<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Filament\Resources\ReportResource\RelationManagers;
use App\Models\Customer;
use App\Models\Report;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;


class ReportResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $pluralModelLabel = 'Тайлан';
    protected static bool $hasTitleCaseModelLabel = false;
    protected static ?string $navigationLabel = 'Тайлан';
    protected static bool $canCreateAnother = false;
    protected static ?string $modelLabel = 'Тайлан';

    protected static ?string $navigationIcon = 'heroicon-c-newspaper';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {

return $table
    ->columns([
        TextColumn::make('payment_type')
            ->formatStateUsing(fn ($state) => config('constants.payment_types')[$state] ?? 'Тодорхойгүй')
            ->label('Төлбөрийн төрөл'),
        TextColumn::make('created_at')
        ->label('огноо')
         ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('Y/m/d')),
         TextColumn::make('pay')
            ->label('Төлбөр')
             ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', ','). ' ₮')
            ->summarize(Sum::make()),
    ])
    ->filters([
        SelectFilter::make('payment_type')
    ->label('Төлбөрийн төрөл')
    ->default('card')
    ->options(config('constants.payment_types')),
        Filter::make('created_at_range')
            ->form([
            
                DatePicker::make('created_from')->label('Эхлэх огноо'),
                DatePicker::make('created_until')->label('Дуусах огноо'),
            ])
            ->query(function (Builder $query, array $data) {
                return $query
                    ->when($data['created_from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                    ->when($data['created_until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
            }),
    ]);
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageReports::route('/'),
        ];
    }
}