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
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use App\Notifications\DocumentExpiryReminder;
use App\Models\Document;
class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->selectRaw('*, DATEDIFF(expiry_date, CURDATE()) as days_left'))
            ->defaultSort('days_left', 'asc') 
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
                TextColumn::make('days_left')
                    ->label('Days Left')
                    ->sortable()
                    ->color(fn (string $state): string => match (true) {
                        $state > 30 => 'success', // More than a month left
                        $state > 7 => 'warning', // Less than a month but more than a week
                        $state <= 7 && $state >= 0 => 'danger', // Less than a week, urgent
                        $state < 0 => 'secondary', // Expired
                        default => 'secondary',
                    })
                    ->alignEnd(), 
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
                                Action::make('send_expiration_email')
                    ->label('Send Reminder')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->action(function (Document $record) {

                        $employee = $record->employee;

                        // Check if the employee relationship exists and is "notifiable"
                        // if (!$employee || !method_exists($employee, 'notify')) {
                        //     Notification::make()
                        //         ->title('Error')
                        //         ->body('Employee record or Notification trait not found.')
                        //         ->danger()
                        //         ->send();
                        //     return;
                        // }

                        // Call the employee's notify method with your custom Notification class
                        $employee->notify(new DocumentExpiryReminder($record));
                        
                        // Confirmation Notification
                        Notification::make()
                            ->title('Reminder Email Queued')
                            ->body("A reminder for the **{$record->documentType->name}** document has been sent to **{$employee->name}**.")
                            ->success()
                            ->send();
                    })
                    // Only show the button if an expiry date and a linked employee exists
                    ->visible(fn (Document $record): bool => (bool) $record->employee && $record->expiry_date) 
                    ->tooltip('Send a manual expiry reminder email to the employee.'),
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
