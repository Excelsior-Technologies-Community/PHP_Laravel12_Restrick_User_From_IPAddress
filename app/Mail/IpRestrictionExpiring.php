<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\IpRestriction;

class IpRestrictionExpiring extends Mailable
{
    use Queueable, SerializesModels;

    public $restriction;

    public function __construct(IpRestriction $restriction)
    {
        $this->restriction = $restriction;
    }

    public function build()
    {
        return $this->subject('⏰ IP Restriction Expiring Soon')
            ->view('emails.ip-restriction-expiring');
    }
}
