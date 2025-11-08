<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Registration;

class CheckKycStep
{
    /**
     * Handle an incoming request.
     *
     * Ensures user completes KYC steps in order.
     */
    public function handle(Request $request, Closure $next, ?int $requiredStep = null): Response
    {
        $userId = session('user_id');

        if (!$userId) {
            return redirect()->route('login');
        }

        $registration = Registration::find($userId);

        if (!$registration) {
            return redirect()->route('login');
        }

        // If step is specified, check if user has reached that step
        if ($requiredStep !== null && $registration->step_number < $requiredStep) {
            return redirect()->route('kyc.form')->with('error', 'Please complete previous steps first');
        }

        // Store registration in request for easy access
        $request->merge(['registration' => $registration]);

        return $next($request);
    }
}
