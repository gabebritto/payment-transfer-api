<?php

namespace App\Modules\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Retailer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected static function newFactory()
    {
        return \Database\Factories\RetailerFactory::new();
    }

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
        'document',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function canSendMoney(): bool
    {
        return false;
    }

    public function wallet(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(\App\Modules\Wallet\Models\Wallet::class, 'owner');
    }
}
