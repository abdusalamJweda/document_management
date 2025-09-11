<?php

namespace App\Filament\Resources\Documents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Support\Enums\Alignment;
use Illuminate\Contracts\View\View;
class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Displays the employee's name from the related 'employee' model.
                TextColumn::make('employee.name')
                    ->sortable()
                    ->searchable(),
                // Displays the document type's name from the related 'documentType' model.
                TextColumn::make('documentType.name')
                    ->label('Document Type')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('file')
                    ->label('File')
                    ->formatStateUsing(fn (): string => 'View File')
                    ->url(fn ($record): string => $record->file)
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-document-text'),
                TextColumn::make('issued_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('submission_date')
                    ->date()
                    ->sortable(),
                IconColumn::make('is_expired')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('view_pdf')
                    ->label('View PDF')
                    ->icon('heroicon-o-eye')
                    ->modalAlignment(Alignment::Center)
                    ->modalHeading(fn ($record) => $record->file)
                    ->modalSubmitAction(false) // This removes the submit button from the modal
                    ->modalCancelActionLabel('Close') // Change the cancel button text to 'Close'
                    ->modalContent(fn ($record): View => view('filament.modals.pdf-viewer', [
                        'record' => $record,
                    ])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
