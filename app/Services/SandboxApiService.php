<?php

namespace App\Services;

use App\Models\SandboxToken;
use App\Models\SandboxBankLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SandboxApiService
{
    protected ?string $apiUrl;
    protected ?string $apiKey;
    protected ?string $secret;

    public function __construct()
    {
        $this->apiUrl = config('services.sandbox.url') ?? 'https://api.sandbox.co.in';
        $this->apiKey = config('services.sandbox.api_key') ?? '';
        $this->secret = config('services.sandbox.secret') ?? '';
    }

    /**
     * Get authentication token.
     */
    public function getToken(): ?string
    {
        // Try to get from cache first
        $token = Cache::get('sandbox_token');
      
        if ($token) {
            return $token;
        }
      
        // Try to get from database
        $tokenRecord = SandboxToken::getCurrentToken();
        if ($tokenRecord) {
            Cache::put('sandbox_token', $tokenRecord, now()->addHours(12));
            return $tokenRecord;
        }

        // Generate new token
        return $this->refreshToken();
    }

    /**
     * Refresh authentication token.
     */
   public function refreshToken(): ?string
{
    try {
        $response = Http::timeout(30)
            ->withHeaders([
                'x-api-key' => $this->apiKey,
                'x-api-secret' => $this->secret,
                'x-api-version' => '1.0',
            ])
            ->post($this->apiUrl . '/authenticate');

        if ($response->successful()) {
            $data = $response->json();

            // Pick correct token from nested or top-level key
            $token = $data['data']['access_token'] ?? $data['access_token'] ?? null;

            if ($token) {
                // Save token in database
                \App\Models\SandboxToken::updateToken($data);

                // Cache token for quick use
                \Cache::put('sandbox_token', $token, now()->addHours(12));

                \Log::info('Sandbox token refreshed successfully.');
                return $token;
            }
        }

        \Log::error('Failed to refresh Sandbox token', [
            'status' => $response->status(),
            'response' => $response->body(),
        ]);
        return null;

    } catch (\Exception $e) {
        \Log::error('Exception refreshing Sandbox token', [
            'error' => $e->getMessage(),
        ]);
        return null;
    }
}


    /**
     * Verify PAN card details.
     */
    public function verifyPan(string $panNumber): array
    {  
        try {
            $token = $this->getToken();
            if (!$token) {
                return [
                    'success' => false,
                    'message' => 'Unable to authenticate with verification service',
                ];
            }

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => $token,
                    'x-api-key' => $this->apiKey,
                    'x-api-version' => '1.0',
                ])
                ->post($this->apiUrl . '/pans/verify', [
                    'pan' => strtoupper($panNumber),
                    'consent' => 'Y',
                    'reason' => 'For KYC verification',
                ]);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('PAN verification successful', [
                    'pan' => $panNumber,
                    'response' => $data,
                ]);

                return [
                    'success' => true,
                    'data' => $data['data'] ?? $data,
                    'name' => $data['data']['name'] ?? null,
                    'status' => $data['data']['status'] ?? null,
                ];
            }

            // If token expired, try refreshing and retry once
            if ($response->status() === 401) {
                $this->refreshToken();
                return $this->verifyPan($panNumber);
            }

            Log::error('PAN verification failed', [
                'pan' => $panNumber,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'PAN verification failed',
                'error' => $response->json()['message'] ?? 'Unknown error',
            ];

        } catch (\Exception $e) {
            Log::error('PAN verification exception', [
                'pan' => $panNumber,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred during PAN verification',
            ];
        }
    }

    /**
     * Verify bank account details.
     */
    public function verifyBankAccount(string $accountNumber, string $ifsc): array
    {
        try {
            $token = $this->getToken();

            if (!$token) {
                return [
                    'success' => false,
                    'message' => 'Unable to authenticate with verification service',
                ];
            }

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => $token,
                    'x-api-key' => $this->apiKey,
                    'x-api-version' => '1.0',
                ])
                ->post($this->apiUrl . '/bank/verify', [
                    'account_number' => $accountNumber,
                    'ifsc' => strtoupper($ifsc),
                ]);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('Bank account verification successful', [
                    'account' => $accountNumber,
                    'ifsc' => $ifsc,
                    'response' => $data,
                ]);

                return [
                    'success' => true,
                    'data' => $data['data'] ?? $data,
                    'account_exists' => $data['data']['account_exists'] ?? false,
                    'name_at_bank' => $data['data']['name_at_bank'] ?? null,
                ];
            }

            // If token expired, try refreshing and retry once
            if ($response->status() === 401) {
                $this->refreshToken();
                return $this->verifyBankAccount($accountNumber, $ifsc);
            }

            Log::error('Bank account verification failed', [
                'account' => $accountNumber,
                'ifsc' => $ifsc,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Bank account verification failed',
                'error' => $response->json()['message'] ?? 'Unknown error',
            ];

        } catch (\Exception $e) {
            Log::error('Bank account verification exception', [
                'account' => $accountNumber,
                'ifsc' => $ifsc,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred during bank account verification',
            ];
        }
    }

    /**
     * Log bank verification attempt.
     */
    public function logBankVerification(int $userId, string $ip, array $data): void
    {
        try {
            SandboxBankLog::logVerification($userId, $ip, $data);
        } catch (\Exception $e) {
            Log::error('Failed to log bank verification', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
