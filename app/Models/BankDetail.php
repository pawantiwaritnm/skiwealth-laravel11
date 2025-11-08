<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankDetail extends Model
{
    protected $table = 'bank_details';

    public $timestamps = false;

    const CREATED_AT = 'added_on';

    protected $fillable = [
        'registration_id',
        'ifsc_code',
        'account_number',
        'account_type',
        'bank',
        'branch',
        'address',
        'micr',
        'name_at_bank',
        'status',
    ];

    protected $casts = [
        'added_on' => 'datetime',
        'status' => 'integer',
    ];

    /**
     * Get the registration that owns the bank details.
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class, 'registration_id');
    }
}
