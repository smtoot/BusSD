<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class WithdrawalMethod extends Model
{
    use GlobalStatus;

    protected $casts = [
        'user_data' => 'object',
    ];

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class, 'method_id');
    }
}
