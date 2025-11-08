<?php

namespace App\Http\Controllers\AccountClosure;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\AccountClosure;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AccountClosureController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show account closure login page.
     */
    public function showLoginPage()
    {
        // If already verified, redirect to closure form
        if (Session::has('account_closure_verified') && Session::has('account_closure_user_id')) {
            return redirect()->route('account.closure.form');
        }

        return view('account_closure.login');
    }

    /**
     * Check if user exists and can request account closure.
     */
    public function checkUser(Request $request)
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

        // Check if user exists
        $registration = Registration::where('mobile', $request->mobile)->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Mobile number not registered',
            ], 422);
        }

        // Check if user already submitted account closure request
        $existingClosure = AccountClosure::where('registration_id', $registration->id)
            ->where('status', 1)
            ->first();

        if ($existingClosure) {
            return response()->json([
                'success' => false,
                'message' => 'Account closure request already submitted',
            ], 422);
        }

        // Send OTP
        $result = $this->otpService->sendAccountClosureOtp($request->mobile);

        if ($result['success']) {
            // Store mobile in session
            Session::put('account_closure_mobile', $request->mobile);
            Session::put('account_closure_user_id', $registration->id);
        }

        return response()->json($result);
    }

    /**
     * Verify OTP for account closure.
     */
    public function verifyOtp(Request $request)
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
        $verified = $this->otpService->verifyOtpFromSession($request->mobile, $request->otp, 'account_closure');

        if (!$verified) {
            $remaining = $this->otpService->getRemainingAttempts($request->mobile, 'account_closure');

            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP',
                'remaining_attempts' => $remaining,
            ], 422);
        }

        // Get user
        $registration = Registration::where('mobile', $request->mobile)->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 422);
        }

        // Set account closure session
        Session::put('account_closure_verified', true);
        Session::put('account_closure_mobile', $request->mobile);
        Session::put('account_closure_user_id', $registration->id);
        Session::put('account_closure_user_name', $registration->name);
        Session::put('account_closure_user_email', $registration->email);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully',
            'redirect' => route('account.closure.form'),
        ]);
    }

    /**
     * Show account closure form.
     */
    public function showClosureForm()
    {
        // Check if verified
        if (!Session::has('account_closure_verified') || !Session::get('account_closure_verified')) {
            return redirect()->route('account.closure.login')->with('error', 'Please verify your mobile number first');
        }

        $userName = Session::get('account_closure_user_name');
        $userEmail = Session::get('account_closure_user_email');

        return view('account_closure.form', compact('userName', 'userEmail'));
    }

    /**
     * Submit account closure request.
     */
    public function submitClosure(Request $request)
    {
        // Check session
        if (!Session::has('account_closure_verified') || !Session::get('account_closure_verified')) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please verify again.',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'dp_id' => 'required|string|max:50',
            'client_master_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // 2MB max
            'reason_for_closure' => 'required|in:Financial Constraints,Service issue,Others',
            'mobile_number' => 'required|digits:10',
            'target_dp_id' => 'required|string|max:50',
            'client_id' => 'required|string|max:50',
            'trading_code' => 'required|string|max:50',
        ], [
            'reason_for_closure.in' => 'Please select a valid reason for closure',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = Session::get('account_closure_user_id');
        $registration = Registration::findOrFail($userId);

        try {
            // Check if already submitted
            $existingClosure = AccountClosure::where('registration_id', $userId)
                ->where('status', 1)
                ->first();

            if ($existingClosure) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account closure request already submitted',
                ], 422);
            }

            $data = [
                'registration_id' => $userId,
                'name' => $request->name,
                'email' => $request->email,
                'dp_id' => $request->dp_id,
                'reason_for_closure' => $request->reason_for_closure,
                'mobile_number' => $request->mobile_number,
                'target_dp_id' => $request->target_dp_id,
                'client_id' => $request->client_id,
                'trading_code' => $request->trading_code,
                'ip' => $request->ip(),
                'status' => 1,
            ];

            // Upload client master file if provided
            if ($request->hasFile('client_master_file')) {
                $filePath = $request->file('client_master_file')->store('account_closure', 'public');
                $data['client_master_file'] = $filePath;
            }

            // Create account closure record
            $closure = AccountClosure::create($data);

            // Generate closure OTP for final verification
            $closureOtp = $this->otpService->generateOtp();
            $this->otpService->storeOtpInSession($request->mobile_number, $closureOtp, 'closure_verification');

            // Send OTP
            $otpSent = $this->otpService->sendOtp($request->mobile_number, $closureOtp, 'closure_verification');

            // Store closure ID in session for OTP verification
            Session::put('account_closure_id', $closure->id);

            Log::info('Account closure request submitted', [
                'user_id' => $userId,
                'closure_id' => $closure->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Account closure request submitted. Please verify OTP sent to your mobile.',
                'requires_otp' => true,
                'otp_sent' => $otpSent,
            ]);

        } catch (\Exception $e) {
            Log::error('Account closure submission failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting. Please try again.',
            ], 500);
        }
    }

    /**
     * Verify final OTP for account closure confirmation.
     */
    public function verifyClosureOtp(Request $request)
    {
        // Check session
        if (!Session::has('account_closure_verified') || !Session::get('account_closure_verified')) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please verify again.',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'otp' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $mobile = Session::get('account_closure_mobile');
        $closureId = Session::get('account_closure_id');

        if (!$closureId) {
            return response()->json([
                'success' => false,
                'message' => 'No closure request found',
            ], 422);
        }

        // Verify OTP
        $verified = $this->otpService->verifyOtpFromSession($mobile, $request->otp, 'closure_verification');

        if (!$verified) {
            $remaining = $this->otpService->getRemainingAttempts($mobile, 'closure_verification');

            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP',
                'remaining_attempts' => $remaining,
            ], 422);
        }

        // Update closure record to mark OTP as verified
        $closure = AccountClosure::findOrFail($closureId);
        $closure->update(['verify_otp' => 1]);

        // Clear all account closure sessions
        Session::forget([
            'account_closure_verified',
            'account_closure_mobile',
            'account_closure_user_id',
            'account_closure_user_name',
            'account_closure_user_email',
            'account_closure_id',
        ]);

        Log::info('Account closure OTP verified', [
            'closure_id' => $closureId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Account closure request verified successfully. Our team will process your request shortly.',
        ]);
    }

    /**
     * Get account closure history for a user.
     */
    public function getHistory(Request $request)
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

        $registration = Registration::where('mobile', $request->mobile)->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $closures = AccountClosure::where('registration_id', $registration->id)
            ->orderBy('added_on', 'desc')
            ->get()
            ->map(function ($closure) {
                return [
                    'id' => $closure->id,
                    'name' => $closure->name,
                    'email' => $closure->email,
                    'dp_id' => $closure->dp_id,
                    'reason' => $closure->reason_for_closure,
                    'mobile' => $closure->mobile_number,
                    'otp_verified' => $closure->verify_otp == 1,
                    'submitted_at' => $closure->added_on ? $closure->added_on->format('d M Y, h:i A') : '',
                    'status' => $closure->getStatusText(),
                ];
            });

        return response()->json([
            'success' => true,
            'closures' => $closures,
        ]);
    }
}
