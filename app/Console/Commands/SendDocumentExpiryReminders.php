<?php
// app/Console/Commands/SendDocumentExpiryReminders.php

namespace App\Console\Commands;

use App\Models\Document;
use App\Notifications\DocumentExpiryReminder;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Log;

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

        $today = Carbon::today();

        // Get documents that are not expired and will expire in exactly 10 days
        $documents = Document::query()
            ->with(['employee', 'documentType'])
            ->whereHas('documentType', function ($query) {
                $query->where('expiry_duration_days', '>', 0);
            })
            ->where('is_expired', false)
            ->get()
            ->filter(function ($document) {
                // Calculate the expiry date
                $issued_at = Carbon::parse($document->issued_date);
                $expiryDate = $issued_at->addDays($document->documentType->expiry_duration_days);
                
                // Check if the expiry date is exactly 10 days from now
                return Carbon::today()->diffInDays($expiryDate, false) <= 10;
            });

        foreach ($documents as $document) {
            $employee = $document->employee;
            if ($employee && $employee->email) {
                $employee->notify(new DocumentExpiryReminder($document));
                $this->info("Sent expiry reminder for document ID {$document->id} to {$employee->email}");
            }
        }

        $this->info('Document expiry reminders sent successfully.');
    }
}