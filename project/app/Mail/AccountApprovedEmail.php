<?php

namespace App\Mail;

use App\Models\User; 
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountApprovedEmail extends Mailable
{
    use Queueable, SerializesModels;

    // 
    public User $user;

    /**
     * 
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * 
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Account Has Been Approved!',
        );
    }

    /**
     * 
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.account_approved', // <-- اسم الـ View
        );
    }
}
