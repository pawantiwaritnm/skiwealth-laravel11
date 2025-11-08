<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected ?string $apiUrl;
    protected ?string $apiKey;
    protected ?string $sender;

    public function __construct()
    {
        $this->apiUrl = config('services.onex_sms.url') ?? 'https://api.onex-aura.com/api/sms';
        $this->apiKey = config('services.onex_sms.api_key') ?? '';
        $this->sender = config('services.onex_sms.sender') ?? 'SKICAP';
    }

    /**
     * Send SMS via Onex SMS Gateway.
     */
    public function sendSms(string $mobile, string $message): bool
    {
        try {
            // Clean mobile number (remove +91, spaces, etc.)
            $mobile = $this->cleanMobileNumber($mobile);

            // Prepare request data
            $data = [
                'apikey' => $this->apiKey,
                'sender' => $this->sender,
                'number' => $mobile,
                'message' => $message,
            ];

            Log::info('Sending SMS', [
                'mobile' => $mobile,
                'message' => $message,
            ]);

            // Send request
            $response = Http::timeout(10)->post($this->apiUrl, $data);

            // Check response
            if ($response->successful()) {
                $result = $response->json();

                Log::info('SMS sent successfully', [
                    'mobile' => $mobile,
                    'response' => $result,
                ]);

                return true;
            }

            Log::error('SMS failed', [
                'mobile' => $mobile,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('SMS exception', [
                'mobile' => $mobile,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Clean mobile number.
     */
    protected function cleanMobileNumber(string $mobile): string
    {
        // Remove all non-numeric characters
        $mobile = preg_replace('/[^0-9]/', '', $mobile);

        // Remove country code if present
        if (strlen($mobile) > 10 && substr($mobile, 0, 2) === '91') {
            $mobile = substr($mobile, 2);
        }

        return $mobile;
    }

    /**
     * Send bulk SMS.
     */
    public function sendBulkSms(array $mobiles, string $message): array
    {
        $results = [];

        foreach ($mobiles as $mobile) {
            $results[$mobile] = $this->sendSms($mobile, $message);
        }

        return $results;
    }

    /**
     * Send templated SMS.
     */
    public function sendTemplate(string $mobile, string $template, array $variables = []): bool
    {
        $message = $this->parseTemplate($template, $variables);
        return $this->sendSms($mobile, $message);
    }

    /**
     * Parse template with variables.
     */
    protected function parseTemplate(string $template, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $template = str_replace("{{$key}}", $value, $template);
        }

        return $template;
    }
}
