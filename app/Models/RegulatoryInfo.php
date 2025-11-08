<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegulatoryInfo extends Model
{
    protected $table = 'regulatory_info';

    public $timestamps = false;

    const CREATED_AT = 'added_on';

    protected $fillable = [
        'registration_id',
        'number_of_years_of_investment',
        'pep',
        'name_of_pep',
        'relation_with_pep',
        'any_action_by_sebi',
        'details_of_action',
        'dealing_with_other_stockbroker',
        'any_dispute_with_stockbroker',
        'dispute_with_stockbroker_details',
        'commodity_trade_classification',
        'status',
    ];

    protected $casts = [
        'added_on' => 'datetime',
        'status' => 'integer',
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class, 'registration_id');
    }
}
