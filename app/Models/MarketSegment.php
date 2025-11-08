<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketSegment extends Model
{
    protected $table = 'market_segments';

    public $timestamps = false;

    const CREATED_AT = 'added_on';

    protected $fillable = [
        'registration_id',
        'cash',
        'futures_options',
        'commodity',
        'currency',
        'mutual_fund',
        'status',
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class, 'registration_id');
    }
}
