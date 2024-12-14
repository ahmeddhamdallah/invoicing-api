<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceGenerated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Invoice
     */
    private $invoice;

    /**
     * Create a new notification instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
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
        $period = [
            'start' => $this->invoice->start_date->format('Y-m-d'),
            'end' => $this->invoice->end_date->format('Y-m-d')
        ];

        return (new MailMessage)
            ->subject('New Invoice Generated')
            ->greeting('Hello!')
            ->line('A new invoice has been generated for your account.')
            ->line("Billing Period: {$period['start']} to {$period['end']}")
            ->line("Total Amount: {$this->invoice->total_amount} SAR")
            ->action('View Invoice', url("/api/v1/invoices/{$this->invoice->id}"))
            ->line('Thank you for using our service!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'amount' => $this->invoice->total_amount,
            'start_date' => $this->invoice->start_date,
            'end_date' => $this->invoice->end_date,
        ];
    }
} 