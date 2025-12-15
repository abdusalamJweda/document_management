@component('mail::message')
# 📅 Document Expiry Reminder

Hello **{{ $notifiable->name }}**,

This is an important compliance reminder. Your document is approaching its expiration date.

## Document Details

* **Type:** **{{ $document->documentType->name }}**
* **Issued Date:** {{ \Carbon\Carbon::parse($document->issued_date)->format('M d, Y') }}
* **Time Left:** Expiring in **10 days or less**

Please ensure you take action to renew or update this document to maintain compliance. The document must be renewed immediately to avoid interruption.

<!-- @component('mail::button', ['url' => url('/documents/' . $document->id), 'color' => 'error'])
View Document Details
@endcomponent -->

If you have already renewed this document, please update your records in the system.

Thank you for your prompt attention to this matter.

@endcomponent