<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nomination extends Model
{
    protected $table = 'nomination';

    public $timestamps = false;

    const CREATED_AT = 'added_on';

    protected $fillable = [
        'registration_id',
        'nominee_minor',
        'guardian_name',
        'guardian_mobile',
        'guardian_email',
        'relation_of_guardian',
        'date_of_birth',
        'guardian_address',
        'guardian_city',
        'guardian_state',
        'guardian_country',
        'guardian_pin_code',
        'guardian_identification',
        'guardian_identification_value',
        'status',
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class, 'registration_id');
    }

    public function nominationDetails()
    {
        return $this->hasMany(NominationDetail::class, 'nomination_id');
    }
}
