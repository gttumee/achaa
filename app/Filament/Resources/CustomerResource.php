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
     protected static ?string $modelLabel = 'Ачаа';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('phone')
                ->label('Утас')
                ->required()
                ->validationMessages([
                'required' => 'Утасны дугаар заавал оруулна уу.',
                 ])
                ->numeric()
                ->mask('99999999')
                ->placeholder('88000011'),
                TextInput::make('second_phone')
                ->label('Утас2')
                ->numeric()
                ->mask('99999999')
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
                ->numeric()
                ->validationMessages([
                'required' => 'Төлбөр заавал оруулна уу',
                   ]),
               Select::make('bairshil_id')
                ->label('Байршил')
                ->relationship('bairshil', 'name')
                ->searchable()
                ->preload()
                  ->validationMessages([
                'required' => 'Байршил заавал сонгоно уу',
                   ])
                ->createOptionForm([
                Forms\Components\TextInput::make('name')
                ->label('Байршил нэр')
                ->required()
                 ->validationMessages([
                'required' => 'Байршил заавал сонгоно уу',
                   ])
                ])
                ->required(),
                 Select::make('payment_type')
                ->label('Төлбөрийн төлөв')
                ->required()
                ->options(config('constants.payment_types')) 
                ->reactive(),
                 Textarea::make('add_content')
                 ->label('Нэмэлт мэдээлэл'),
                 Textarea::make('content')
                ->label('Тайлбар'),
                 Select::make('shipping_type')
                ->label('Хүргэлийн төлөв')
                ->options(config('constants.come')) 
                ->default('not_come') 
                ->reactive(),
                Hidden::make('user_id')
                ->default(fn () => auth()->id())
                ->dehydrated(),
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
                           ->color(fn ($state) => $state === 'not_pay' ? 'danger' : 'success')
                            ->sortable()
                            ->icon('heroicon-o-credit-card')
                            ->formatStateUsing(fn ($state) => config('constants.payment_types')[$state] ?? 'Тодорхойгүй')
                            ->alignLeft(),
                    Tables\Columns\TextColumn::make('shipping_type')
                            ->alignLeft()
                            ->badge()
                             ->color(fn ($state) => $state === 'come' ? 'success' : 'danger')
                            ->searchable()
                            ->label('Төлөв')
                            ->sortable()
                            ->icon('heroicon-o-truck')
                            ->formatStateUsing(fn ($state) => config('constants.come')[$state] ?? 'Тодорхойгүй')
                            ->alignLeft(),
                    ])->space(2),
                    Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('bairshil.name')
                    ->sortable()
                    ->label('Байршил')
                    ->searchable()
                    ->alignLeft()
                    ->formatStateUsing(fn ($state) => 'Байршил: ' . ($state ?? 'Тодорхойгүй')),
                      Tables\Columns\TextColumn::make('created_at')
                            ->alignLeft()
                            ->searchable()
                             ->icon('heroicon-o-calendar')
                             ->sortable()
                             ->badge()
                            ->label('Хүргэсэн өдөр')
                            ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state))
                    ])->space(2),
                ])->from('md')
            ])
             ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                 Tables\Actions\ViewAction::make()
                 ->color('primary')
                 ->button(),
                Tables\Actions\EditAction::make()
                ->button(),
        Tables\Actions\Action::make('shipping_type')
            ->label('Хүргэгдээгүй')
            ->icon('heroicon-o-x-circle')
            ->visible(fn ($record) => $record->shipping_type !== 'come')
            ->requiresConfirmation()
            ->modalHeading('Та энэ ачааг хүргэгдсэн гэж бүртгэх үү？')
            ->modalDescription('Энэ үйлдэл нь тухайн хэрэглэгчийг хүргэгдсэн төлөвт шилжинэ')
            ->modalButton('Тийм, хадгалах')
            ->color('danger')
            ->button()
    ->action(function ($record) {
        $record->update([
            'shipping_type' => 'come',
            'shipping_date' => now()  
        ]);
    }),


    Tables\Actions\Action::make('alreadyShipped')
        ->label('Хүргэгдсэн')
        ->disabled()
        ->button()
        ->visible(fn ($record) => $record->shipping_type === 'come')
        ->color('success')
        ->icon('heroicon-o-check-circle'),
          Tables\Actions\DeleteAction::make()
                 ->label('') 
                ->visible(fn ($record) => auth()->user()?->role === 'admin'),
]);
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         Tables\Actions\DeleteBulkAction::make(),
            //     ]),
            // ]);
    }
    
public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Fieldset::make('Холбоо барих')
                ->schema([
                TextEntry::make('phone')
                 ->weight(FontWeight::Bold)
                ->size(TextEntry\TextEntrySize::Large)
                ->label('Утас'),
                TextEntry::make('second_phone')
                 ->weight(FontWeight::Bold)
                 ->size(TextEntry\TextEntrySize::Large)
                ->label('Утас2'),
                ]),
            Fieldset::make('Төлбөр')
                ->schema([
                TextEntry::make('pay')
                ->weight(FontWeight::Bold)
                ->label('Төлбөр')
                ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', ','). ' ₮'),
                TextEntry::make('payment_type')
                ->weight(FontWeight::Bold)
                ->label('Төлбөрийн төрөл')
                ->formatStateUsing(fn ($state) => config('constants.payment_types')[$state] ?? 'Тодорхойгүй'),  
                TextEntry::make('transfer_cost')
                ->weight(FontWeight::Bold)
                ->label('Тээврийн зардал')
                ->formatStateUsing(fn ($state) => number_format((float) $state, 0, '.', ','). ' ₮'),
                TextEntry::make('payment_content')
                ->label('Төлбөрийн утга'),
                   ]),
                   Fieldset::make('Бусад мэдээлэл')
                ->schema([
                  TextEntry::make('bairshil.name')
                  ->size(TextEntry\TextEntrySize::Large)
                 ->label('Байршил')
                ->formatStateUsing(fn ($state) => ($state ?? 'Тодорхойгүй')),
                TextEntry::make('shipping_type')
                ->weight(FontWeight::Bold)
                ->label('Хүргэгдсэн эсэх')
                ->formatStateUsing(fn ($state) => config('constants.come')[$state] ?? 'Тодорхойгүй'),  
                 TextEntry::make('content')
                 ->weight(FontWeight::Bold)
                ->label('Тайлбар'),
                TextEntry::make('add_content')
                ->weight(FontWeight::Bold)
                ->label('Нэмэлт тайлбар'),
                    TextEntry::make('user.name')
                ->weight(FontWeight::Bold)
                ->label('Бичилт хийсэн'),
                ]),
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
            // 'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}