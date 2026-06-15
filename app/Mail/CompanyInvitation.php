<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompanyInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public string $companyName;
    public string $inviteUrl;

    public function __construct(string $companyName, string $inviteUrl)
    {
        $this->companyName = $companyName;
        $this->inviteUrl   = $inviteUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Company Portal Invitation');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.company_invitation');
    }
}
