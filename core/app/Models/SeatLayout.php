<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class SeatLayout extends Model
{
    use GlobalStatus;
    
    protected $fillable = [
        'owner_id',
        'name',
        'layout',
        'schema',
        'status'
    ];

    protected $casts = [
        'schema' => 'object'
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function getHasVisualMappingAttribute()
    {
        return !empty($this->schema) && 
               isset($this->schema->meta) && 
               isset($this->schema->layout);
    }

    public function getGridRowsAttribute()
    {
        return $this->schema->meta->grid->rows ?? 10;
    }

    public function getGridColsAttribute()
    {
        return $this->schema->meta->grid->cols ?? 5;
    }
}
