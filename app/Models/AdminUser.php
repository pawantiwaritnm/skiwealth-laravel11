<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'last_login',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'last_login' => 'datetime',
        'status' => 'integer',
    ];
}
