<?php

namespace App\Console\Commands;

use App\Models\EmailVerificationCode;
use Illuminate\Console\Command;

class CleanupExpiredVerificationCodes extends Command
{
    protected $signature = 'verification:cleanup';
    protected $description = 'Clean up expired email verification codes';

    public function handle()
    {
        EmailVerificationCode::cleanupExpired();
        $this->info('Expired verification codes cleaned up successfully.');
    }
}
