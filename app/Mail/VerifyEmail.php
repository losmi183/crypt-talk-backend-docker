<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $username;
    public string $email;
    public string $verificationUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $userData, string $registerToken)
    {
        $this->username = $userData["name"] ?? "";
        $this->email = $userData["email"] ?? "";

        $frontendUrl = env('APP_ENV') === 'production' 
            ? env('BACKEND_PROD') 
            : env('BACKEND_DEV');
        
                // 2. Create verification URL
        $this->verificationUrl = $frontendUrl . '/api/auth/verify-email?verify_token=' . $registerToken;

    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Submit your email address',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.verify-email',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
