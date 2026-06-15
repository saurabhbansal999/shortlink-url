<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MemberInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public string $companyName;
    public string $inviteUrl;
    public string $roleLabel;

    public function __construct(string $companyName, string $inviteUrl, string $roleLabel = 'Member')
    {
        $this->companyName = $companyName;
        $this->inviteUrl   = $inviteUrl;
        $this->roleLabel   = $roleLabel;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You've been invited to join {$this->companyName} as {$this->roleLabel}"
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.member_invitation');
    }
}
