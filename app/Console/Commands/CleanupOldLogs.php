<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BlockedIpLog;
use App\Models\IpRestriction;
use Illuminate\Support\Facades\Mail;
use App\Mail\BlockedIpAlert;

class CleanupOldLogs extends Command
{
    protected $signature = 'ip-restriction:cleanup-logs';
    protected $description = 'Delete blocked IP logs older than the retention period';

    public function handle(): void
    {
        $retentionDays = (int) config('ip-restriction.log_retention_days', 90);
        $cutoffDate = now()->subDays($retentionDays);

        $deleted = BlockedIpLog::where('blocked_at', '<', $cutoffDate)->delete();

        $this->info("Deleted {$deleted} blocked IP log(s) older than {$retentionDays} days.");
    }
}
