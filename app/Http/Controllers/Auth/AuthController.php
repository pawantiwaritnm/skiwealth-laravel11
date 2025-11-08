<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Show registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle user registration - Step 1: Send OTP.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile' => 'required|digits:10|unique:registration,mobile',
            'referral_code' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check if mobile already exists
        $existingUser = Registration::where('mobile', $request->mobile)->first();
        if ($existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'Mobile number already registered',
            ], 422);
        }

        // Store user data in session
        Session::put('registration_data', $request->only(['name', 'email', 'mobile', 'referral_code']));

        // Send OTP
        $result = $this->otpService->sendRegistrationOtp($request->mobile);
        // dd($result);
        return response()->json($result);
    }

    /**
     * Verify OTP and complete registration.
     */
    public function verifyRegistrationOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|digits:10',
            'otp' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Verify OTP
        $verified = $this->otpService->verifyOtpFromSession($request->mobile, $request->otp, 'registration');

        if (!$verified) {
            $remaining = $this->otpService->getRemainingAttempts($request->mobile, 'registration');

            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP',
                'remaining_attempts' => $remaining,
            ], 422);
        }

        // Get registration data from session
        $registrationData = Session::get('registration_data');

        if (!$registrationData) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please try again.',
            ], 422);
        }

        // Create registration
        $registration = Registration::create([
            'name' => $registrationData['name'],
            'email' => $registrationData['email'],
            'mobile' => $registrationData['mobile'],
            'referral_code' => $registrationData['referral_code'] ?? null,
            'mobile_status' => 1, // Mobile verified
            'status' => 1,
            'step_number' => 1,
        ]);

        // Clear session data
        Session::forget('registration_data');

        // Log user in
        Session::put('user_id', $registration->id);
        Session::put('user_mobile', $registration->mobile);
        Session::put('user_name', $registration->name);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'redirect' => route('kyc.form'),
        ]);
    }

    /**
     * Handle login - Send OTP.
     */
    public function sendLoginOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|digits:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->otpService->sendLoginOtp($request->mobile);

        return response()->json($result);
    }

    /**
     * Verify login OTP.
     */
    public function verifyLoginOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|digits:10',
            'otp' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Get user
        $registration = Registration::where('mobile', $request->mobile)->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Mobile number not registered',
            ], 422);
        }

        // Verify OTP from session first
        $verified = $this->otpService->verifyOtpFromSession($request->mobile, $request->otp, 'login');

        // If not verified from session, check database
        if (!$verified) {
            $verified = $this->otpService->verifyOtpFromDatabase($registration, $request->otp);
        }

        if (!$verified) {
            $remaining = $this->otpService->getRemainingAttempts($request->mobile, 'login');

            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP',
                'remaining_attempts' => $remaining,
            ], 422);
        }

        // Log user in
        Session::put('user_id', $registration->id);
        Session::put('user_mobile', $registration->mobile);
        Session::put('user_name', $registration->name);

        // Clear OTP
        $registration->update(['otp_number' => null]);

        // Determine redirect based on step
        $redirect = $this->getRedirectUrl($registration);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'redirect' => $redirect,
        ]);
    }

    /**
     * Get redirect URL based on registration step.
     */
    protected function getRedirectUrl(Registration $registration): string
    {
        if ($registration->isComplete()) {
            return route('thank-you');
        }

        return route('kyc.form');
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Session::flush();

        return redirect()->route('login')->with('message', 'Logged out successfully');
    }

    /**
     * Resend OTP.
     */
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|digits:10',
            'type' => 'required|in:registration,login,ipv,account_closure',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $type = $request->type;
        $mobile = $request->mobile;

        $result = match($type) {
            'registration' => $this->otpService->sendRegistrationOtp($mobile),
            'login' => $this->otpService->sendLoginOtp($mobile),
            'ipv' => $this->otpService->sendIpvOtp($mobile),
            'account_closure' => $this->otpService->sendAccountClosureOtp($mobile),
        };

        return response()->json($result);
    }
}
