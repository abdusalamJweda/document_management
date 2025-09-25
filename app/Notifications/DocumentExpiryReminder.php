<?php

// app/Notifications/DocumentExpiryReminder.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Document;

class DocumentExpiryReminder extends Notification
{
    use Queueable;

    protected $document;

    /**
     * Create a new notification instance.
     */
    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Document Expiry Reminder')
            ->greeting('Hello, ' . $notifiable->name . '!')
            ->line('Your document "' . $this->document->documentType->name . '" is expiring in 10 days.')
            ->line('Issued Date: ' . $this->document->issued_date->format('M d, Y'))
            ->line('Please take the necessary steps to renew it.')
            ->action('View Document', url('/documents/' . $this->document->id))
            ->line('Thank you!');
    }
}
