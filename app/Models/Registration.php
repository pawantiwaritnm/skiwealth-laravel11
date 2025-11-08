<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Registration extends Model
{
    protected $table = 'registration';

    public $timestamps = false;

    const CREATED_AT = 'added_on';

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'referral_code',
        'status',
        'mobile_status',
        'step_number',
        'application_number',
        'application_date',
        'otp_number',
        'reg_flag',
        'kyc_uploaded',
        'webhook_data',
        'webhook_status',
    ];

    protected $casts = [
        'application_date' => 'datetime',
        'added_on' => 'datetime',
        'status' => 'integer',
        'mobile_status' => 'integer',
        'step_number' => 'integer',
        'reg_flag' => 'integer',
        'kyc_uploaded' => 'integer',
        'webhook_status' => 'integer',
    ];

    /**
     * Get the personal details for the registration.
     */
    public function personalDetail(): HasOne
    {
        return $this->hasOne(PersonalDetail::class, 'registration_id');
    }

    /**
     * Get the address for the registration.
     */
    public function address(): HasOne
    {
        return $this->hasOne(Address::class, 'registration_id');
    }

    /**
     * Get the bank details for the registration.
     */
    public function bankDetail(): HasOne
    {
        return $this->hasOne(BankDetail::class, 'registration_id');
    }

    /**
     * Get the market segments for the registration.
     */
    public function marketSegment(): HasOne
    {
        return $this->hasOne(MarketSegment::class, 'registration_id');
    }

    /**
     * Get the regulatory info for the registration.
     */
    public function regulatoryInfo(): HasOne
    {
        return $this->hasOne(RegulatoryInfo::class, 'registration_id');
    }

    /**
     * Get the KYC documents for the registration.
     */
    public function kycDocument(): HasOne
    {
        return $this->hasOne(KycDocument::class, 'registration_id');
    }

    /**
     * Get the nomination for the registration.
     */
    public function nomination(): HasOne
    {
        return $this->hasOne(Nomination::class, 'registration_id');
    }

    /**
     * Get all IPV capture videos for the registration.
     */
    public function captureVideos(): HasMany
    {
        return $this->hasMany(UserCaptureVideo::class, 'registration_id');
    }

    /**
     * Get account closure requests for the registration.
     */
    public function accountClosures(): HasMany
    {
        return $this->hasMany(AccountClosure::class, 'registration_id');
    }

    /**
     * Scope a query to only include active registrations.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope a query to only include verified mobile numbers.
     */
    public function scopeMobileVerified($query)
    {
        return $query->where('mobile_status', 1);
    }

    /**
     * Check if registration is complete (all steps done).
     */
    public function isComplete(): bool
    {
        return $this->step_number >= 6 && $this->kyc_uploaded == 1;
    }

    /**
     * Generate and set application number.
     */
    public function generateApplicationNumber(): string
    {
        $prefix = 'SKI';
        $year = date('Y');
        $month = date('m');
        $lastRecord = self::orderBy('id', 'desc')->first();
        $nextId = $lastRecord ? $lastRecord->id + 1 : 1;

        $applicationNumber = $prefix . $year . $month . str_pad($nextId, 6, '0', STR_PAD_LEFT);

        $this->application_number = $applicationNumber;
        $this->save();

        return $applicationNumber;
    }

    /**
     * Get full user information with all relationships.
     */
    public function getFullUserInfo()
    {
        return $this->load([
            'personalDetail',
            'address',
            'bankDetail',
            'marketSegment',
            'regulatoryInfo',
            'kycDocument',
            'nomination.nominationDetails',
        ]);
    }
}
