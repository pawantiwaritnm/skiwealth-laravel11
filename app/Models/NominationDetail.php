<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NominationDetail extends Model
{
    protected $table = 'nomination_details';

    public $timestamps = false;

    const CREATED_AT = 'added_on';

    protected $fillable = [
        'nomination_id',
        'name_of_nominee',
        'nominee_mobile',
        'nominee_email',
        'share_of_nominees',
        'relation_applicant_name_nominees',
        'nominee_address',
        'nominee_city',
        'nominee_state',
        'nominees_country',
        'nominee_pin_code',
        'nominee_identification',
        'nominee_document',
        'status',
    ];

    public function nomination()
    {
        return $this->belongsTo(Nomination::class, 'nomination_id');
    }
}
