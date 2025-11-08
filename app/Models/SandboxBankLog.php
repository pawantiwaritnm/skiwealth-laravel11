<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SandboxBankLog extends Model
{
    protected $table = 'sandbox_bank_log';

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    protected $fillable = [
        'registration_id',
        'ip',
        'api_endpoint',
        'request_payload',
        'response_payload',
        'status_code',
        'verification_type',
        'success',
        'error_message',
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class, 'registration_id');
    }
}
