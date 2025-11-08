<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class BankVerificationService
{
    protected string $ifscApiUrl;

    public function __construct()
    {
        $this->ifscApiUrl = config('services.razorpay.ifsc_url');
    }

    /**
     * Get bank details from IFSC code using Razorpay API.
     */
    public function getBankDetailsByIfsc(string $ifscCode): array
    {
        try {
            $ifscCode = strtoupper(trim($ifscCode));

            // Check cache first
            $cacheKey = "ifsc_details_{$ifscCode}";
            $cachedData = Cache::get($cacheKey);

            if ($cachedData) {
                return $cachedData;
            }

            $response = Http::timeout(10)
                ->get("{$this->ifscApiUrl}/{$ifscCode}");

            if ($response->successful()) {
                $data = $response->json();

                $result = [
                    'success' => true,
                    'bank' => $data['BANK'] ?? '',
                    'branch' => $data['BRANCH'] ?? '',
                    'address' => $data['ADDRESS'] ?? '',
                    'city' => $data['CITY'] ?? '',
                    'district' => $data['DISTRICT'] ?? '',
                    'state' => $data['STATE'] ?? '',
                    'contact' => $data['CONTACT'] ?? '',
                    'micr' => $data['MICR'] ?? '',
                    'ifsc' => $data['IFSC'] ?? $ifscCode,
                ];

                // Cache for 24 hours
                Cache::put($cacheKey, $result, now()->addHours(24));

                Log::info('IFSC details fetched successfully', [
                    'ifsc' => $ifscCode,
                ]);

                return $result;
            }

            if ($response->status() === 404) {
                return [
                    'success' => false,
                    'message' => 'Invalid IFSC code',
                ];
            }

            Log::error('IFSC API failed', [
                'ifsc' => $ifscCode,
                'status' => $response->status(),
            ]);

            return [
                'success' => false,
                'message' => 'Unable to fetch bank details',
            ];

        } catch (\Exception $e) {
            Log::error('IFSC API exception', [
                'ifsc' => $ifscCode,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while fetching bank details',
            ];
        }
    }

    /**
     * Validate IFSC code format.
     */
    public function isValidIfscFormat(string $ifscCode): bool
    {
        // IFSC format: 4 letters (bank code) + 0 + 6 alphanumeric (branch code)
        // Example: SBIN0001234
        return (bool) preg_match('/^[A-Z]{4}0[A-Z0-9]{6}$/', strtoupper($ifscCode));
    }

    /**
     * Format bank address for display.
     */
    public function formatBankAddress(array $bankDetails): string
    {
        $parts = [];

        if (!empty($bankDetails['address'])) {
            $parts[] = $bankDetails['address'];
        }

        if (!empty($bankDetails['city'])) {
            $parts[] = $bankDetails['city'];
        }

        if (!empty($bankDetails['district'])) {
            $parts[] = $bankDetails['district'];
        }

        if (!empty($bankDetails['state'])) {
            $parts[] = $bankDetails['state'];
        }

        return implode(', ', $parts);
    }
}
