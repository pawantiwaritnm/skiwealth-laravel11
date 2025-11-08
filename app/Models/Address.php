<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $table = 'address';

    public $timestamps = false;

    const CREATED_AT = 'added_on';

    protected $fillable = [
        'registration_id',
        'permanent_address',
        'permanent_address1',
        'permanent_address2',
        'permanent_address_city',
        'permanent_address_country',
        'permanent_address_pincode',
        'is_same',
        'correspondence_address',
        'correspondence_address1',
        'correspondence_address2',
        'correspondence_address_city',
        'correspondence_address_country',
        'correspondence_address_pincode',
        'status',
    ];

    protected $casts = [
        'added_on' => 'datetime',
        'is_same' => 'integer',
        'status' => 'integer',
    ];

    /**
     * Get the registration that owns the address.
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class, 'registration_id');
    }

    /**
     * Check if correspondence address is same as permanent.
     */
    public function isSameAddress(): bool
    {
        return $this->is_same == 1;
    }

    /**
     * Copy permanent address to correspondence address.
     */
    public function copyPermanentToCorrespondence(): void
    {
        $this->correspondence_address = $this->permanent_address;
        $this->correspondence_address1 = $this->permanent_address1;
        $this->correspondence_address2 = $this->permanent_address2;
        $this->correspondence_address_city = $this->permanent_address_city;
        $this->correspondence_address_country = $this->permanent_address_country;
        $this->correspondence_address_pincode = $this->permanent_address_pincode;
        $this->is_same = 1;
    }
}
