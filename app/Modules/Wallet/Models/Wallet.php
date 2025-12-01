<?php

namespace App\Modules\Wallet\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\WalletFactory::new();
    }

    protected $fillable = ['owner_type', 'owner_id', 'balance'];

    public function owner()
    {
        return $this->morphTo();
    }
}
