<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KycDocument extends Model
{
    protected $table = 'kyc_documents';

    public $timestamps = false;

    const CREATED_AT = 'added_on';
    const UPDATED_AT = 'updated_on';

    protected $fillable = [
        'registration_id',
        'pan_card_front',
        'pan_card_back',
        'aadhaar_front',
        'aadhaar_back',
        'photo',
        'signature',
        'bank_proof',
        'income_proof',
        'address_proof',
        'status',
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class, 'registration_id');
    }
}
