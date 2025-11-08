<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SandboxToken extends Model
{
    protected $table = 'sandbox_token';

    public $timestamps = false;

    protected $fillable = [
        'access_token',
        'token_type',
        'expires_in',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the latest active and valid token.
     */
    public static function getCurrentToken()
    {
        return self::where('status', 1)
            ->where('expires_at', '>', Carbon::now())
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * Insert or update a token record.
     */
    public static function updateToken(array $tokenResponse)
    {
        // Deactivate previous tokens
        self::query()->update(['status' => 0]);

        // Extract access_token from different possible response formats
        $accessToken = $tokenResponse['data']['access_token']
            ?? $tokenResponse['access_token']
            ?? null;

        if (!$accessToken) {
            \Log::error('SandboxToken::updateToken => No access_token found', [
                'response' => $tokenResponse,
            ]);
            return null;
        }

        $expiresIn = $tokenResponse['expires_in'] ?? 86400; // default 24h

        return self::create([
            'access_token' => $accessToken,
            'token_type'   => 'Bearer',
            'expires_in'   => $expiresIn,
            'expires_at'   => now()->addSeconds($expiresIn),
            'status'       => 1,
        ]);
    }
}
