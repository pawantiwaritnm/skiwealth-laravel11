<?php

namespace App\Services;

use App\Models\Registration;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class OtpService
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Generate a random OTP.
     */
    public function generateOtp(int $length = 6): string
    {
        return str_pad((string) random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    }

    /**
     * Send OTP to mobile number.
     */
    public function sendOtp(string $mobile, string $otp, string $type = 'login'): bool
    {
        try {
            // For local testing, skip SMS and just log OTP
            if (config('app.env') === 'local' || config('app.debug')) {
                Log::info("OTP for {$mobile} ({$type}): {$otp}");
                return true; // Always return success in local/debug mode
            }

            $message = $this->getOtpMessage($otp, $type);
            return $this->smsService->sendSms($mobile, $message);
        } catch (\Exception $e) {
            Log::error('OTP send failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get OTP message based on type.
     */
    protected function getOtpMessage(string $otp, string $type): string
    {
        return match($type) {
            'login' => "Your OTP for SKI Capital login is: {$otp}. Valid for 10 minutes.",
            'registration' => "Your OTP for SKI Capital registration is: {$otp}. Valid for 10 minutes.",
            'ipv' => "Your OTP for SKI Capital IPV verification is: {$otp}. Valid for 10 minutes.",
            'account_closure' => "Your OTP for SKI Capital account closure is: {$otp}. Valid for 10 minutes.",
            default => "Your OTP is: {$otp}. Valid for 10 minutes.",
        };
    }

    /**
     * Store OTP in session.
     */
    public function storeOtpInSession(string $mobile, string $otp, string $type = 'login'): void
    {
        Session::put("otp_{$type}_{$mobile}", [
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0,
        ]);
    }

    /**
     * Verify OTP from session.
     */
    public function verifyOtpFromSession(string $mobile, string $otp, string $type = 'login'): bool
    {
        $key = "otp_{$type}_{$mobile}";
        $data = Session::get($key);

        if (!$data) {
            return false;
        }

        // Check if expired
        if (now()->greaterThan($data['expires_at'])) {
            Session::forget($key);
            return false;
        }

        // Check attempts
        if ($data['attempts'] >= 3) {
            Session::forget($key);
            return false;
        }

        // Verify OTP
        if ($data['otp'] === $otp) {
            Session::forget($key);
            return true;
        }

        // Increment attempts
        $data['attempts']++;
        Session::put($key, $data);

        return false;
    }

    /**
     * Store OTP in database (for Registration model).
     */
    public function storeOtpInDatabase(Registration $registration, string $otp): void
    {
        $registration->update(['otp_number' => $otp]);
    }

    /**
     * Verify OTP from database.
     */
    public function verifyOtpFromDatabase(Registration $registration, string $otp): bool
    {
        return $registration->otp_number === $otp;
    }

    /**
     * Generate and send OTP for registration.
     */
    public function sendRegistrationOtp(string $mobile): array
    {
        $otp = $this->generateOtp();

        // Store in session
        $this->storeOtpInSession($mobile, $otp, 'registration');

        // Send SMS
        $sent = $this->sendOtp($mobile, $otp, 'registration');

        $message = $sent ? 'OTP sent successfully' : 'Failed to send OTP';

        // For local testing, include OTP in message
        if (config('app.env') === 'local' || config('app.debug')) {
            $message .= ". Your OTP is: {$otp}";
        }

        return [
            'success' => $sent,
            'message' => $message,
            'otp' => config('app.debug') ? $otp : null, // Only in debug mode
        ];
    }

    /**
     * Generate and send OTP for login.
     */
    public function sendLoginOtp(string $mobile): array
    {
        // Check if user exists
        $registration = Registration::where('mobile', $mobile)->first();

        if (!$registration) {
            return [
                'success' => false,
                'message' => 'Mobile number not registered',
            ];
        }

        $otp = $this->generateOtp();

        // Store in session and database
        $this->storeOtpInSession($mobile, $otp, 'login');
        $this->storeOtpInDatabase($registration, $otp);

        // Send SMS
        $sent = $this->sendOtp($mobile, $otp, 'login');

        $message = $sent ? 'OTP sent successfully' : 'Failed to send OTP';

        // For local testing, include OTP in message
        if (config('app.env') === 'local' || config('app.debug')) {
            $message .= ". Your OTP is: {$otp}";
        }

        return [
            'success' => $sent,
            'message' => $message,
            'otp' => config('app.debug') ? $otp : null, // Only in debug mode
        ];
    }

    /**
     * Generate and send OTP for IPV.
     */
    public function sendIpvOtp(string $mobile): array
    {
        $registration = Registration::where('mobile', $mobile)->first();

        if (!$registration) {
            return [
                'success' => false,
                'message' => 'Mobile number not registered',
            ];
        }

        $otp = $this->generateOtp();

        // Store in session
        $this->storeOtpInSession($mobile, $otp, 'ipv');

        // Send SMS
        $sent = $this->sendOtp($mobile, $otp, 'ipv');

        return [
            'success' => $sent,
            'message' => $sent ? 'OTP sent successfully' : 'Failed to send OTP',
            'otp' => config('app.debug') ? $otp : null,
        ];
    }

    /**
     * Generate and send OTP for account closure.
     */
    public function sendAccountClosureOtp(string $mobile): array
    {
        $registration = Registration::where('mobile', $mobile)->first();

        if (!$registration) {
            return [
                'success' => false,
                'message' => 'Mobile number not registered',
            ];
        }

        $otp = $this->generateOtp();

        // Store in session
        $this->storeOtpInSession($mobile, $otp, 'account_closure');

        // Send SMS
        $sent = $this->sendOtp($mobile, $otp, 'account_closure');

        return [
            'success' => $sent,
            'message' => $sent ? 'OTP sent successfully' : 'Failed to send OTP',
            'otp' => config('app.debug') ? $otp : null,
        ];
    }

    /**
     * Clear OTP from session.
     */
    public function clearOtp(string $mobile, string $type = 'login'): void
    {
        Session::forget("otp_{$type}_{$mobile}");
    }

    /**
     * Get remaining attempts.
     */
    public function getRemainingAttempts(string $mobile, string $type = 'login'): int
    {
        $data = Session::get("otp_{$type}_{$mobile}");
        return $data ? (3 - $data['attempts']) : 0;
    }
}
