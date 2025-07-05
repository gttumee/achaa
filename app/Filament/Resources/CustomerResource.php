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
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Support\RawJs;
use Filament\Notifications\Notification;
use Filament\Infolists\Components\Fieldset;





class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray'; // ирсэн
    protected static ?string $pluralModelLabel = 'Явсан ачаа';
    protected static bool $hasTitleCaseModelLabel = false;
    protected static ?string $navigationLabel = 'Явсан ачаа';
     protected static bool $canCreateAnother = false;

    public static function form(Form $form): Form
    {
        return $form
      
            ->schema([
                TextInput::make('phone')
                ->label('Утас')
                ->required()
                ->numeric()
                ->minLength(8)
                ->maxLength(8)
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
               Hidden::make('logistic_type')
                ->default('outgoing')
                ->dehydrated(true),
                TextInput::make('pay')
                ->placeholder('500,000')
                ->required()
                ->label('Төлбөр')
                ->mask(RawJs::make('$money($input)'))
                 ->stripCharacters(',')
                ->numeric(),
                 Select::make('payment_status')
                ->label('Төлбөрийн статус')
                ->options(config('constants.payment_status')) 
                ->default('not_payd') 
                ->reactive()
                  ->disabled(fn ($livewire) =>
                    $livewire instanceof \Filament\Resources\Pages\CreateRecord
                 ),
                Select::make('aimag')
                ->label('Байршил')
                ->searchable()
                ->options(config('constants.aimag'))
                ->reactive(),
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
                           ->formatStateUsing(fn ($state) => 'Байршил: ' . (config('constants.aimag')[$state] ?? 'Тодорхойгүй')),
                     Tables\Columns\TextColumn::make('created_at')
                            ->alignLeft()
                            ->searchable()
                             ->sortable()
                            ->formatStateUsing(fn ($state) => 'Огноо: ' .$state),
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
                 ->color('primary'),
                // Төлөөгүй үед гарч ирэх action
                Tables\Actions\Action::make('payment_status')
                 ->label('Төлөгдөөгүй')
                ->icon('heroicon-o-x-circle')
                ->visible(fn ($record) => $record->payment_status !== 'payd')
                ->requiresConfirmation()
                ->modalHeading('Та энэ төлбөрийг төлсөн гэж бүртгэх үү？')
                ->modalDescription('Доорх мэдээллийг оруулна уу')
                ->modalButton('Тийм, хадгалах')
                ->color('danger')
                ->button()
    ->form([
        Select::make('payment_type')
            ->required()
            ->label('Төлбөрийн хэлбэр')
            ->options(config('constants.payment_types'))
            ->reactive()
            ->afterStateUpdated(fn ($set) => $set('payment_contenct', null)),

        TextInput::make('payment_content')
            ->label('Төлбөрийн утга')
            ->required(),
    ])

    ->action(function ($record, array $data) {
        $record->update([
            'payment_status' => 'payd',
            'payment_type' => $data['payment_type'],
            'payment_content' => $data['payment_content'],
        ]);
    }),

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
                Fieldset::make('Холбоо барих')
                ->schema([
                TextEntry::make('phone')
                 ->weight(FontWeight::Bold)
                ->label('Утас'),
                TextEntry::make('second_phone')
                 ->weight(FontWeight::Bold)
                ->label('Утас2'),
                ]),
            Fieldset::make('Төлбөр')
                ->schema([
                TextEntry::make('pay')
                ->weight(FontWeight::Bold)
                ->label('Төлбөр'),
                TextEntry::make('payment_type')
                ->weight(FontWeight::Bold)
                ->label('Төлбөрийн төрөл')
                ->formatStateUsing(fn ($state) => config('constants.payment_types')[$state] ?? 'Тодорхойгүй'),  
                TextEntry::make('transfer_cost')
                ->weight(FontWeight::Bold)
                ->label('Тээврийн зардал'),             
                TextEntry::make('payment_content')
                ->label('Төлбөрийн утга'),
                   ]),
                   Fieldset::make('Бусад мэдээлэл')
                ->schema([
                TextEntry::make('aimag')
                ->weight(FontWeight::Bold)
                ->label('Байршил')
                ->formatStateUsing(fn ($state) =>(config('constants.aimag')[$state] ?? 'Тодорхойгүй')),
                 TextEntry::make('content')
                 ->weight(FontWeight::Bold)
                ->label('Тайлбар'),
                TextEntry::make('add_content')
                ->weight(FontWeight::Bold)
                ->label('Нэмэлт тайлбар'),
                ])
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