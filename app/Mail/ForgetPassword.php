<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class ForgetPassword extends Mailable
{
    use Queueable, SerializesModels;

    private $token;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public User $user, String $token)
    {
        $this->token = $token;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            from: new Address('kenidyryan@gmail.com', 'Almon'),
            subject: 'Forget Password',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        $user = Auth::user();
        return new Content(
            view: 'forget_password',
            with: [
                'token' => $this->token,
                'user' => $user
            ]
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
