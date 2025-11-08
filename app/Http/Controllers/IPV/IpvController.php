<?php

namespace App\Http\Controllers\IPV;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\UserCaptureVideo;
use App\Services\OtpService;
use App\Services\RecaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class IpvController extends Controller
{
    protected OtpService $otpService;
    protected RecaptchaService $recaptchaService;

    public function __construct(OtpService $otpService, RecaptchaService $recaptchaService)
    {
        $this->otpService = $otpService;
        $this->recaptchaService = $recaptchaService;
    }

    /**
     * Show IPV permission/login page.
     */
    public function showPermissionPage()
    {
        $siteKey = $this->recaptchaService->getSiteKey('ipv');

        return view('ipv.permission', compact('siteKey'));
    }

    /**
     * Verify user for IPV with reCAPTCHA.
     */
    public function checkUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|digits:10',
            'g-recaptcha-response' => 'required',
        ], [
            'g-recaptcha-response.required' => 'Please complete the reCAPTCHA verification',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Verify reCAPTCHA
        if ($this->recaptchaService->isEnabled('ipv')) {
            $recaptchaResult = $this->recaptchaService->verify(
                $request->input('g-recaptcha-response'),
                'ipv'
            );

            if (!$recaptchaResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'reCAPTCHA verification failed. Please try again.',
                ], 422);
            }
        }

        // Check if user exists
        $registration = Registration::where('mobile', $request->mobile)->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Mobile number not registered',
            ], 422);
        }

        // Check if KYC is complete
        if (!$registration->isComplete()) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete your KYC application first',
            ], 422);
        }

        // Check IPV submission limit (allow max 3 attempts)
        $ipvCount = UserCaptureVideo::where('registration_id', $registration->id)->count();

        if ($ipvCount >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'You have reached the maximum number of IPV attempts',
            ], 422);
        }

        // Send OTP
        $result = $this->otpService->sendIpvOtp($request->mobile);

        if ($result['success']) {
            // Store mobile in session for IPV
            Session::put('ipv_mobile', $request->mobile);
            Session::put('ipv_user_id', $registration->id);
        }

        return response()->json($result);
    }

    /**
     * Verify OTP for IPV.
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
        $verified = $this->otpService->verifyOtpFromSession($request->mobile, $request->otp, 'ipv');

        if (!$verified) {
            $remaining = $this->otpService->getRemainingAttempts($request->mobile, 'ipv');

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

        // Set IPV session
        Session::put('ipv_verified', true);
        Session::put('ipv_mobile', $request->mobile);
        Session::put('ipv_user_id', $registration->id);
        Session::put('ipv_user_name', $registration->name);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully',
            'redirect' => route('ipv.camera'),
        ]);
    }

    /**
     * Show camera permission page.
     */
    public function showCameraPage()
    {
        // Check if IPV session is verified
        if (!Session::has('ipv_verified') || !Session::get('ipv_verified')) {
            return redirect()->route('ipv.permission')->with('error', 'Please verify your mobile number first');
        }

        $userName = Session::get('ipv_user_name');

        return view('ipv.camera', compact('userName'));
    }

    /**
     * Record and save IPV video.
     */
    public function recordVideo(Request $request)
    {
        // Check IPV session
        if (!Session::has('ipv_verified') || !Session::get('ipv_verified')) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please verify again.',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'video' => 'required|file|mimes:mp4,webm,mov|max:10240', // 10MB max
            'image' => 'required|file|mimes:jpg,jpeg,png|max:2048', // 2MB max
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = Session::get('ipv_user_id');
        $registration = Registration::findOrFail($userId);

        try {
            // Check submission limit
            $ipvCount = UserCaptureVideo::where('registration_id', $userId)->count();

            if ($ipvCount >= 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maximum IPV attempts reached',
                ], 422);
            }

            // Upload video
            $videoPath = $request->file('video')->store('ipv_videos', 'public');

            // Upload image (screenshot)
            $imagePath = $request->file('image')->store('ipv_images', 'public');

            // Create IPV record
            $ipvRecord = UserCaptureVideo::create([
                'registration_id' => $userId,
                'user_video' => $videoPath,
                'image' => $imagePath,
                'lat' => $request->latitude,
                'lng' => $request->longitude,
                'city' => $request->city,
                'state' => $request->state,
                'ip' => $request->ip(),
                'ipv_otp' => Session::get("otp_ipv_{$registration->mobile}.otp"),
                'status' => 1,
            ]);

            // Clear IPV session
            Session::forget(['ipv_verified', 'ipv_mobile', 'ipv_user_id', 'ipv_user_name']);

            Log::info('IPV video recorded successfully', [
                'user_id' => $userId,
                'ipv_id' => $ipvRecord->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'IPV video recorded successfully',
                'ipv_id' => $ipvRecord->id,
            ]);

        } catch (\Exception $e) {
            Log::error('IPV video recording failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while recording. Please try again.',
            ], 500);
        }
    }

    /**
     * Upload video in base64 format (alternative method).
     */
    public function uploadBase64Video(Request $request)
    {
        // Check IPV session
        if (!Session::has('ipv_verified') || !Session::get('ipv_verified')) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please verify again.',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'video_data' => 'required|string',
            'image_data' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = Session::get('ipv_user_id');
        $registration = Registration::findOrFail($userId);

        try {
            // Check submission limit
            $ipvCount = UserCaptureVideo::where('registration_id', $userId)->count();

            if ($ipvCount >= 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maximum IPV attempts reached',
                ], 422);
            }

            // Decode and save video
            $videoData = $request->video_data;
            if (preg_match('/^data:video\/(\w+);base64,/', $videoData, $type)) {
                $videoData = substr($videoData, strpos($videoData, ',') + 1);
                $type = strtolower($type[1]);

                $videoData = base64_decode($videoData);

                if ($videoData === false) {
                    throw new \Exception('Base64 decode failed for video');
                }

                $videoFileName = 'ipv_video_' . $userId . '_' . time() . '.' . $type;
                $videoPath = 'ipv_videos/' . $videoFileName;

                Storage::disk('public')->put($videoPath, $videoData);
            } else {
                throw new \Exception('Invalid video data format');
            }

            // Decode and save image
            $imageData = $request->image_data;
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $type = strtolower($type[1]);

                $imageData = base64_decode($imageData);

                if ($imageData === false) {
                    throw new \Exception('Base64 decode failed for image');
                }

                $imageFileName = 'ipv_image_' . $userId . '_' . time() . '.' . $type;
                $imagePath = 'ipv_images/' . $imageFileName;

                Storage::disk('public')->put($imagePath, $imageData);
            } else {
                throw new \Exception('Invalid image data format');
            }

            // Create IPV record
            $ipvRecord = UserCaptureVideo::create([
                'registration_id' => $userId,
                'user_video' => $videoPath,
                'image' => $imagePath,
                'lat' => $request->latitude,
                'lng' => $request->longitude,
                'city' => $request->city,
                'state' => $request->state,
                'ip' => $request->ip(),
                'ipv_otp' => Session::get("otp_ipv_{$registration->mobile}.otp"),
                'status' => 1,
            ]);

            // Clear IPV session
            Session::forget(['ipv_verified', 'ipv_mobile', 'ipv_user_id', 'ipv_user_name']);

            Log::info('IPV video uploaded successfully (base64)', [
                'user_id' => $userId,
                'ipv_id' => $ipvRecord->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'IPV video uploaded successfully',
                'ipv_id' => $ipvRecord->id,
            ]);

        } catch (\Exception $e) {
            Log::error('IPV video upload failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while uploading. Please try again.',
            ], 500);
        }
    }

    /**
     * Get user's IPV history.
     */
    public function getHistory(Request $request)
    {
        $mobile = $request->input('mobile');

        if (!$mobile) {
            return response()->json([
                'success' => false,
                'message' => 'Mobile number required',
            ], 422);
        }

        $registration = Registration::where('mobile', $mobile)->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $ipvRecords = UserCaptureVideo::where('registration_id', $registration->id)
            ->orderBy('added_on', 'desc')
            ->get()
            ->map(function ($record) {
                return [
                    'id' => $record->id,
                    'recorded_at' => $record->added_on,
                    'location' => $record->getLocationString(),
                    'has_location' => $record->hasLocation(),
                    'video_url' => $record->getVideoUrl(),
                    'image_url' => $record->getImageUrl(),
                ];
            });

        return response()->json([
            'success' => true,
            'count' => $ipvRecords->count(),
            'max_attempts' => 3,
            'remaining_attempts' => 3 - $ipvRecords->count(),
            'records' => $ipvRecords,
        ]);
    }
}
