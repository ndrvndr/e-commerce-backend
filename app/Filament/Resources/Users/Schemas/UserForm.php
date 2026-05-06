<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                Hidden::make('email_verified_at')
                    ->default(now()),
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->searchable()
                    ->required()
                    ->preload(),
                TextInput::make('password')
                    ->password()
                    ->required(),
            ]);
    }
}
