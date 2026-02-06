<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class ForgotPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $username;
    public string $email;
    public string $resetUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, string $forgotPasswordToken)
    {
        $this->username = $user->name ?? "";
        $this->email = $user->email ?? "";

        $frontendUrl = env('APP_ENV') === 'production' 
            ? env('FRONTEND_PROD') 
            : env('FRONTEND_DEV');
        
                // 2. Create verification URL
        $this->resetUrl = $frontendUrl . '/reset-password/' . $forgotPasswordToken;

    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Reset password link',
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
            view: 'emails.reset-password',
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
