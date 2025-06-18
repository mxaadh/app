<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureEmailIsVerifiedApiMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user() || ! $request->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Your email address is not verified.',
                'email_verification_required' => true
            ], 403);
        }

        return $next($request);
    }
}
