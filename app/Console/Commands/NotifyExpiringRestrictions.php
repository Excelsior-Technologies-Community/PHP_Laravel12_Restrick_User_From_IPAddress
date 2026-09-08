<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\IpRestriction;
use Illuminate\Support\Facades\Mail;
use App\Mail\BlockedIpAlert;

class NotifyExpiringRestrictions extends Command
{
    protected $signature = 'ip-restriction:notify-expiring';
    protected $description = 'Send email notifications for IP restrictions expiring soon';

    public function handle(): void
    {
        $upcoming = IpRestriction::where('is_active', true)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now()->addDays(7))
            ->where('expires_at', '>', now())
            ->get();

        foreach ($upcoming as $restriction) {
            try {
                Mail::to(config('ip-restriction.alert_email'))
                    ->send(new \App\Mail\IpRestrictionExpiring($restriction));
            } catch (\Throwable $e) {
                $this->error("Failed to send notification for {$restriction->ip_address}: {$e->getMessage()}");
            }
        }

        $this->info("Sent {$upcoming->count()} expiring restriction notification(s).");
    }
}
