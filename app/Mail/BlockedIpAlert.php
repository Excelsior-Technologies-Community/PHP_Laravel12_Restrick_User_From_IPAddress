<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;

class BlockedIpAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $ipAddress;
    public $path;
    public $method;

    public function __construct(string $ipAddress, string $path, string $method)
    {
        $this->ipAddress = $ipAddress;
        $this->path = $path;
        $this->method = $method;
    }

    public function build()
    {
        return $this->subject('🚨 Blocked IP Access Alert')
            ->view('emails.blocked-ip-alert');
    }
}
