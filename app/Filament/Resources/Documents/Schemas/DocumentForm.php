<?php

namespace App\Filament\Resources\Documents\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Schemas\Schema;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Use a Select component with a relationship to allow choosing an employee by name.
                // It assumes you have a 'name' column in your Employee model.
                Select::make('employee_id')
                    ->relationship('employee', 'id')
                     ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                    ->searchable()
                    ->preload() // Makes the list of employees searchable
                    ->required(),

                // Use a Select component with a relationship to allow choosing a document type by name.
                // It assumes you have a 'name' column in your DocumentType model.
                Select::make('document_type_id')
                    ->relationship('documentType', 'name')
                    ->searchable() // Makes the list of document types searchable
                    ->required(),
                    
                SpatieMediaLibraryFileUpload::make('files')
                    ->collection('files'),
                
                DatePicker::make('issued_date')
                    ->required(),
                
                DatePicker::make('submission_date')
                    ->required(),
                
                Toggle::make('is_expired')
                    ->required(),
            ]);
    }
}
