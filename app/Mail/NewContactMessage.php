<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    public $message;

    public function __construct(ContactMessage $message)
    {
        $this->message = $message;
    }

    public function build()
    {
        return $this->subject('New Contact Message from ' . $this->message->name)
                    ->markdown('emails.contact.new-message');
    }
} 