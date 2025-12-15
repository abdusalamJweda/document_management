<?php

namespace App\Filament\Resources\Employees;

use App\Filament\Resources\Employees\Pages\CreateEmployee;
use App\Filament\Resources\Employees\Pages\EditEmployee;
use App\Filament\Resources\Employees\Pages\ListEmployees;
use App\Filament\Resources\Employees\Schemas\EmployeeForm;
use App\Filament\Resources\Employees\Tables\EmployeesTable;
use App\Models\Employee;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Filament\Resources\Employees\Pages\ViewEmployee;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use App\Models\Document;
use Filament\Infolists\Components\RepeatableEntry;
class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return EmployeeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmployeesTable::configure($table);
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
            'index' => ListEmployees::route('/'),
            'create' => CreateEmployee::route('/create'),
            'view' => ViewEmployee::route('/{record}'),
            'edit' => EditEmployee::route('/{record}/edit'),
        ];
    }

public static function infolist(Schema $schema): Schema
{
    return $schema
        ->components([
            Section::make('Employee Details')
                // ... (Your existing employee details components)
                ->columns(2)
                ->components([
                    TextEntry::make('name'),
                    
                    TextEntry::make('email')
                        ->label('Email Address'),
                    TextEntry::make('employee_id'),
                    TextEntry::make('job_title'),
                ]),
            
           
            Section::make('Associated Documents')
                ->columns(1)
                ->schema([
                    // Use RepeatableEntry with the relationship name 'documents'
                    RepeatableEntry::make('documents')
                        ->label('Employee Files')
                        ->schema([
                            TextEntry::make('documentType.name')
                                ->label('Document Type'),

                            TextEntry::make('issued_date')
                                ->label('Issue Date')
                                ->date(),

                            TextEntry::make('expiry_date')
                                ->label('Expiry Date')
                                ->date(),

                            // Component to show the file name as a download link
                            TextEntry::make('file_download')
                                ->label('Download File')
                                // Get the file name from the 'files' media collection
                                ->getStateUsing(fn (Document $record): ?string => $record->getFirstMedia('files')?->file_name)
                                // Set the URL for the link to the media file
                                ->url(fn (Document $record): ?string => $record->getFirstMediaUrl('files'), true)
                                ->icon('heroicon-o-arrow-down-tray')
                                ->color('primary')
                                ->openUrlInNewTab(),
                        ])
                        ->columns(4)
                        ->contained(),
                        // The line ->emptyStateDescription(...) has been removed.
                ]),
        ]);
}
}
