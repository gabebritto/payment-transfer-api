<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'payer_id',
        'payer_type',
        'payee_id',
        'payee_type',
        'value',
        'status',
    ];

    public function payer()
    {
        return $this->morphTo();
    }

    public function payee()
    {
        return $this->morphTo();
    }
}
