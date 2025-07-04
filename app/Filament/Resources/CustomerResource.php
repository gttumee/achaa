<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Support\RawJs;
use Filament\Notifications\Notification;




class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $pluralModelLabel = 'Ачаа бүртгэл';
    protected static bool $hasTitleCaseModelLabel = false;
    protected static ?string $navigationLabel = 'Ачаа бүртгэл';
     protected static bool $canCreateAnother = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('phone')
                ->label('Утас')
                ->required()
                ->numeric()
                ->placeholder('88000011'),
                TextInput::make('second_phone')
                ->label('Утас2')
                ->numeric()
                ->placeholder('88000011'),
                TextInput::make('transfer_cost')
                ->label('Тээврийн зардал')
                ->placeholder('250,000')
                  ->mask(RawJs::make('$money($input)'))
                 ->stripCharacters(',')
                ->numeric(),
                TextInput::make('pay')
                ->placeholder('500,000')
                ->required()
                ->label('Төлбөр')
                ->mask(RawJs::make('$money($input)'))
                 ->stripCharacters(',')
                ->numeric(),
                Select::make('payment_type')
                ->required()
                ->label('Төлбөрийн хэлбэр')
                ->options(config('constants.payment_types'))
                ->reactive()
                 ->afterStateUpdated(fn ($set) => $set('payment_types', null)),
                 Select::make('payment_status')
                ->label('Төлбөрийн статус')
                ->options(config('constants.payment_status')) 
                ->default('not_payd') 
                ->reactive(),
                Select::make('aimag')
                ->label('Аймаг')
                ->searchable()
                ->options(config('constants.aimag'))
                ->reactive(),
                Select::make('sum')
                ->label('Сум')
                ->options(config('constants.sum'))
                ->reactive()
                ->searchable(),
                 Textarea::make('add_content')
                 ->label('Нэмэлт мэдээлэл'),
                 Textarea::make('content')
                ->label('Тайлбар')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                  Tables\Columns\Layout\Split::make([
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('phone')
                            ->label('Утас')
                            ->searchable()
                            ->sortable()
                            ->color('gray')
                            ->icon('heroicon-m-phone')
                            ->alignLeft(),
                        Tables\Columns\TextColumn::make('second_phone')
                            ->label('Утас')
                            ->searchable()
                            ->sortable()
                            ->color('gray')
                            ->icon('heroicon-m-phone')
                            ->alignLeft(),
                    ]),
                    Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('pay')
                            ->alignLeft()
                            ->color('info')
                            ->label('Төлбөр')
                            ->badge()
                            ->searchable()
                            ->sortable()
                            ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', ','). ' ₮')
                            ->icon('heroicon-s-document-currency-yen')
                            ->alignLeft(),
                    Tables\Columns\TextColumn::make('payment_type')
                            ->alignLeft()
                            ->badge()
                            ->searchable()
                            ->color('info')
                            ->label('Төлбөрийн төрөл')
                            ->sortable()
                            ->icon('heroicon-o-credit-card')
                            ->formatStateUsing(fn ($state) => config('constants.payment_types')[$state] ?? 'Тодорхойгүй')
                            ->alignLeft(),
                    ])->space(2),
                      Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('transfer_cost')
                    ->alignLeft()
                    ->sortable()
                    ->searchable()
                    ->label('Тээврийн зардал')
                    ->formatStateUsing(fn ($state) => 'Тээврийн зардал: ' . number_format((float) $state, 0, '.', ',') . ' ₮'),
                    Tables\Columns\TextColumn::make('aimag')
                           ->sortable()
                            ->label('Аймаг')
                            ->searchable()
                            ->alignLeft()
                           ->formatStateUsing(fn ($state) => 'Аймаг: ' . (config('constants.aimag')[$state] ?? 'Тодорхойгүй')),
                     Tables\Columns\TextColumn::make('sum')
                            ->alignLeft()
                            ->searchable()
                             ->sortable()
                            ->label('Сум')
                            ->formatStateUsing(fn ($state) => 'Сум: ' . (config('constants.sum')[$state] ?? 'Тодорхойгүй')),                   
                    ])->space(2),
                ])->from('md')
            ])
             ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                 Tables\Actions\ViewAction::make()
                 ->label('Дэлгэрэнгүй')
                 ->button()
                 ->color('info'),
                // Төлөөгүй үед гарч ирэх action
    Tables\Actions\Action::make('payment_status')
        ->label('Төлөгдөөгүй')
        ->visible(fn ($record) => $record->payment_status !== 'payd')
        ->requiresConfirmation()
        ->modalHeading('Та энэ төлбөрийг төлсөн гэж бүртгэх үү？')
        ->modalDescription('Та мэдээллээ сайтар шалгана уу.')
        ->modalButton('Тийм, хадгалах')
        ->color('danger')
        ->button()
        ->action(fn ($record) => $record->update(['payment_status' => 'payd'])),

    // Төлсөн үед гарч ирэх action
    Tables\Actions\Action::make('alreadyPayd')
        ->label('Төлөгдсөн')
        ->disabled()
        ->button()
        ->visible(fn ($record) => $record->payment_status === 'payd')
        ->color('success')
        ->icon('heroicon-o-check-circle'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('name')
                ->label('Нэр'),
                TextEntry::make('phone')
                ->label('Утас'),
                TextEntry::make('second_phone')
                ->label('Утас2'),
                TextEntry::make('pay')
                ->label('Төлбөр'),
                TextEntry::make('payment_type')
                ->label('Төлбөрийн төрөл')
                ->formatStateUsing(fn ($state) =>(config('constants.payment_types')[$state] ?? 'Тодорхойгүй')),
                TextEntry::make('aimag')
                ->label('Аймаг')
                ->formatStateUsing(fn ($state) =>(config('constants.aimag')[$state] ?? 'Тодорхойгүй')),
                TextEntry::make('sum')
                ->label('Сум')
                ->formatStateUsing(fn ($state) =>(config('constants.sum')[$state] ?? 'Тодорхойгүй')),
                 TextEntry::make('content')
                ->label('Тайлбар'),
                TextEntry::make('add_content')
                ->label('Нэмэлт'),
            ]);
    }
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}