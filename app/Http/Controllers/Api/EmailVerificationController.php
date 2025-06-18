<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmailVerificationCode;
use App\Models\User;
use App\Notifications\EmailVerificationCode as EmailVerificationCodeNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EmailVerificationController extends Controller
{
    /**
     * Send new verification code
     */
    public function sendVerificationCode(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified.'
            ], 200);
        }

        // Generate and send new verification code
        $code = EmailVerificationCode::createForEmail($user->email);
        $user->notify(new EmailVerificationCodeNotification($code));

        return response()->json([
            'message' => 'Verification code sent to your email.'
        ], 200);
    }

    /**
     * Verify email with 4-digit code
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:4|regex:/^[0-9]{4}$/'
        ]);

        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified.'
            ], 200);
        }

        // Verify the code
        if (!EmailVerificationCode::verifyCode($user->email, $request->code)) {
            throw ValidationException::withMessages([
                'code' => ['The verification code is invalid or has expired.'],
            ]);
        }

        // Mark email as verified
        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json([
            'message' => 'Email verified successfully!',
            'email_verified' => true
        ], 200);
    }

    /**
     * Check verification status
     */
    public function checkStatus(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'is_verified' => $user->hasVerifiedEmail(),
            'email' => $user->email
        ]);
    }

    /**
     * Resend verification code (public endpoint for non-authenticated users)
     */
    public function resendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified.'
            ], 200);
        }

        // Generate and send new verification code
        $code = EmailVerificationCode::createForEmail($user->email);
        $user->notify(new EmailVerificationCodeNotification($code));

        return response()->json([
            'message' => 'Verification code sent to your email.'
        ], 200);
    }
}
