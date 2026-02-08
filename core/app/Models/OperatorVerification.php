<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperatorVerification extends Model
{
    protected $fillable = [
        'owner_id',
        'document_type',
        'document_file',
        'status',
        'admin_feedback'
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
}
