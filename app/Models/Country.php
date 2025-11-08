<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'country';

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    protected $fillable = [
        'sortname',
        'country',
        'phonecode',
        'status',
    ];
}
