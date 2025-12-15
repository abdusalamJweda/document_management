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
use App\Models\EmailLog;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\DB; // <-- Add this import

class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // 👇 MODIFICATION START: Filter the query to get the latest document ID for each employee and document type.
            ->modifyQueryUsing(function (Builder $query) {
                // Select the maximum document ID for each unique combination of employee_id and document_type_id
                $latestDocuments = DB::table('documents')
                    ->select(DB::raw('MAX(id)'))
                    ->groupBy('employee_id', 'document_type_id');

                // Filter the main query to only include documents whose ID is in the subquery result
                $query->whereIn('id', $latestDocuments);

                // Add the days_left calculation
                $query->selectRaw('*, DATEDIFF(expiry_date, CURDATE()) as days_left');
            })
            // MODIFICATION END 👆
            ->defaultSort('days_left', 'asc') 
            ->defaultGroup('employee.name')
            ->columns([
                // Displays the employee's name from the related 'employee' model.
                // TextColumn::make('employee.name')
                //     ->sortable()
                //     ->searchable(),
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
                // 👇 Added SelectFilter for Employee Name
                SelectFilter::make('employee_name')
                    ->relationship('employee', 'name')
                    ->label('Employee')
                    ->searchable() // Allows searching within the dropdown
                    ->preload(), // Loads all options initially for a better UX
                SelectFilter::make('department')
                    // Traverses Document -> employee -> department relationship and uses the department's 'name'
                    ->relationship('employee.department', 'name') 
                    ->label('Department')
                    ->multiple() // This enables selecting multiple departments
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                                Action::make('send_expiration_email')
                    ->label('Send Reminder')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->action(function (Document $record) {
                        
                        try {
                            $employee = $record->employee;

                        $employee->notify(new DocumentExpiryReminder($record));
                        EmailLog::create([
                            'employee_id' => $employee->id,
                            'document_id' => $record->id,
                            'document_type_id' => $record->document_type_id,
                        ]);

                        Notification::make()
                            ->title('Reminder Email Queued')
                            ->body("A reminder for the **{$record->documentType->name}** document has been sent to **{$employee->name}**.")
                            ->success()
                            ->send();
                        } catch (\Throwable $th) {
                            throw $th;
                        }
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