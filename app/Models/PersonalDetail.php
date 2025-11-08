<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalDetail extends Model
{
    protected $table = 'personal_details';

    public $timestamps = false;

    const CREATED_AT = 'added_on';

    protected $fillable = [
        'registration_id',
        'father_name',
        'mother_name',
        'dob',
        'gender',
        'occupation',
        'marital_status',
        'pan_no',
        'pan_name',
        'aadhaar_number',
        'residential_status',
        'annual_income',
        'status',
    ];

    protected $casts = [
        'dob' => 'date',
        'added_on' => 'datetime',
        'status' => 'integer',
    ];

    /**
     * Get the registration that owns the personal details.
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class, 'registration_id');
    }
}
