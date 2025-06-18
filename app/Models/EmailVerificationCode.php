<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EmailVerificationCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'code',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Generate a 4-digit verification code
     */
    public static function generateCode(): string
    {
        return str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create or update verification code for email
     */
    public static function createForEmail(string $email): string
    {
        // Delete any existing codes for this email
        self::where('email', $email)->delete();

        $code = self::generateCode();

        self::create([
            'email' => $email,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(10), // Code expires in 10 minutes
        ]);

        return $code;
    }

    /**
     * Verify code for email
     */
    public static function verifyCode(string $email, string $code): bool
    {
        $verificationCode = self::where('email', $email)
            ->where('code', $code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($verificationCode) {
            // Delete the used code
            $verificationCode->delete();
            return true;
        }

        return false;
    }

    /**
     * Clean up expired codes
     */
    public static function cleanupExpired(): void
    {
        self::where('expires_at', '<', Carbon::now())->delete();
    }
}

