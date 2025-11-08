<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaService
{
    /**
     * Verify reCAPTCHA response.
     */
    public function verify(string $response, string $type = 'ipv'): array
    {
        try {
            $secretKey = $this->getSecretKey($type);

            if (!$secretKey) {
                return [
                    'success' => false,
                    'message' => 'reCAPTCHA not configured',
                ];
            }

            $verifyResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $response,
                'remoteip' => request()->ip(),
            ]);

            if ($verifyResponse->successful()) {
                $result = $verifyResponse->json();

                if ($result['success'] ?? false) {
                    Log::info('reCAPTCHA verification successful', [
                        'type' => $type,
                        'score' => $result['score'] ?? null,
                    ]);

                    return [
                        'success' => true,
                        'score' => $result['score'] ?? null,
                    ];
                }

                Log::warning('reCAPTCHA verification failed', [
                    'type' => $type,
                    'error_codes' => $result['error-codes'] ?? [],
                ]);

                return [
                    'success' => false,
                    'message' => 'reCAPTCHA verification failed',
                    'error_codes' => $result['error-codes'] ?? [],
                ];
            }

            return [
                'success' => false,
                'message' => 'Unable to verify reCAPTCHA',
            ];

        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification exception', [
                'type' => $type,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred during reCAPTCHA verification',
            ];
        }
    }

    /**
     * Get secret key based on type.
     */
    protected function getSecretKey(string $type): ?string
    {
        return match($type) {
            'ipv' => config('services.recaptcha.ipv.secret_key'),
            'nomination' => config('services.recaptcha.nomination.secret_key'),
            default => null,
        };
    }

    /**
     * Get site key based on type.
     */
    public function getSiteKey(string $type): ?string
    {
        return match($type) {
            'ipv' => config('services.recaptcha.ipv.site_key'),
            'nomination' => config('services.recaptcha.nomination.site_key'),
            default => null,
        };
    }

    /**
     * Check if reCAPTCHA is enabled.
     */
    public function isEnabled(string $type = 'ipv'): bool
    {
        return !empty($this->getSiteKey($type)) && !empty($this->getSecretKey($type));
    }
}
