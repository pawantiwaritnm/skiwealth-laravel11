<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountClosure extends Model
{
    protected $table = 'account_closure_tbl';

    protected $fillable = [
        'registration_id',
        'name',
        'email',
        'dp_id',
        'client_master_file',
        'reason_for_closure',
        'mobile_number',
        'target_dp_id',
        'client_id',
        'trading_code',
        'ip',
        'verify_otp',
        'status',
    ];

    protected $casts = [
        'added_on' => 'datetime',
        'updated_on' => 'datetime',
        'status' => 'integer',
        'verify_otp' => 'integer',
    ];

    const CREATED_AT = 'added_on';
    const UPDATED_AT = 'updated_on';

    /**
     * Get the registration that owns the account closure.
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class, 'registration_id');
    }

    /**
     * Get status text.
     */
    public function getStatusText(): string
    {
        if ($this->verify_otp == 1 && $this->status == 1) {
            return 'Verified & Submitted';
        }

        if ($this->status == 1) {
            return 'Pending Verification';
        }

        return 'Inactive';
    }

    /**
     * Check if OTP is verified.
     */
    public function isOtpVerified(): bool
    {
        return $this->verify_otp == 1;
    }

    /**
     * Get formatted submission date.
     */
    public function getFormattedDate(): string
    {
        return $this->added_on ? $this->added_on->format('d M Y, h:i A') : '';
    }

    /**
     * Get client master file URL.
     */
    public function getFileUrl(): string
    {
        return $this->client_master_file ? asset('storage/' . $this->client_master_file) : '';
    }

    /**
     * Check if has client master file.
     */
    public function hasClientMasterFile(): bool
    {
        return !empty($this->client_master_file);
    }
}
