<?php

namespace App\Filament\Resources\EmailLogs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EmailLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('employee_id')
                    ->required()
                    ->numeric(),
                TextInput::make('document_type_id')
                    ->required()
                    ->numeric(),
                TextInput::make('document_id')
                    ->required()
                    ->numeric(),
            ]);
    }
}
