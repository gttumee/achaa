<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-s-user';
    protected static ?string $pluralModelLabel = 'Хэрэглэгч';
    protected static bool $hasTitleCaseModelLabel = false;
    protected static ?string $navigationLabel = 'Хэрэглэгч';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            TextInput::make('name')->required()->label('Нэр'),
            TextInput::make('email')->email()->required()->unique()->label('И-мэйл'),
            TextInput::make('password')
                ->label('Нууц үг')
                ->password()
                ->required(fn (string $context) => $context === 'create')
                ->label('Нууц үг'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            TextColumn::make('name')->label('Нэр'),
            TextColumn::make('email')->label('Имэйл'),
            TextColumn::make('created_at')->label('Бүртгүүлсэн')->dateTime(),
      
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}