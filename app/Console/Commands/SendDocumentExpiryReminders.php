<?php
// app/Console/Commands/SendDocumentExpiryReminders.php

namespace App\Console\Commands;

use App\Models\Document;
use App\Models\EmailLog;
use App\Notifications\DocumentExpiryReminder;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Log;
use Symfony\Component\Mime\Email;

class SendDocumentExpiryReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:remind-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders for documents expiring in 10 days.';

    /**
     * Execute the console command.
     */
    public function handle()
{
    Log::info('Document expiry reminders started successfully at '.now());

    // 1. Get the IDs of the latest documents for each employee and type
    // This prevents reminders for old versions of the same document type.
    $latestDocumentIds = Document::query()
        ->selectRaw('MAX(id) as id')
        ->groupBy('employee_id', 'document_type_id')
        ->pluck('id');

    // 2. Query only those specific IDs
    $documents = Document::query()
        ->whereIn('id', $latestDocumentIds)
        ->with(['employee', 'documentType'])
        ->whereHas('documentType', function ($query) {
            $query->where('expiry_duration_days', '>', 0);
        })
        ->get()
        ->filter(function ($document) {
            // Use the expiry_date directly if it exists in your DB, 
            // otherwise calculate it from issued_date
            $expiryDate = $document->expiry_date 
                ? Carbon::parse($document->expiry_date)
                : Carbon::parse($document->issued_date)->addDays($document->documentType->expiry_duration_days);
            
            // Check if it expires within the next 10 days and is not already expired
            $daysUntilExpiry = Carbon::today()->diffInDays($expiryDate, false);
            
            return $daysUntilExpiry <= 10 && $daysUntilExpiry >= 0;
        });

    foreach ($documents as $document) {
        $employee = $document->employee;
        
        // Ensure we haven't already sent a reminder today to avoid duplicates 
        // if the command runs multiple times
        if ($employee && $employee->email && $document->last_reminder_date?->diffInDays(now()) !== 0) {
            try {
                $employee->notify(new DocumentExpiryReminder($document));
                
                EmailLog::create([
                    'employee_id' => $employee->id,
                    'document_id' => $document->id,
                    'document_type_id' => $document->document_type_id,
                ]);

                $document->update(['last_reminder_date' => Carbon::now()]);
                
                $this->info("Sent expiry reminder for document ID {$document->id} to {$employee->email}");
            } catch (\Throwable $th) {
                Log::error("Failed to send reminder for Doc ID {$document->id}: " . $th->getMessage());
            }
        }
    }

    $this->info('Document expiry reminders process completed.');
}
}