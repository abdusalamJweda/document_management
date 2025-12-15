<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
class EmployeeForm
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
                TextInput::make('job_title')
                    ->required(),
                Select::make('department_id')
                    ->label('Department') // Use a user-friendly label
                    ->relationship('department', 'name') // The crucial part
                    ->searchable() // Optional: Makes finding departments easier
                    ->preload() // Optional: Loads all options on initial view
                    ->required(),
                TextInput::make('employee_id')
                    ->required(),
            ]);
    }
}
