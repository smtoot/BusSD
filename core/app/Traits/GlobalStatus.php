<?php

namespace App\Traits;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait GlobalStatus
{
    public static function changeStatus($id, $column = 'status')
    {
        $modelName = get_class();
        $query     = $modelName::findOrFail($id);

        // Security Fix: Check ownership if model has owner_id
        if (\Illuminate\Support\Facades\Schema::hasColumn($query->getTable(), 'owner_id')) {
            $owner = auth()->guard('owner')->user();
            if ($owner && $query->owner_id != $owner->id && !auth()->guard('admin')->check()) {
                abort(403, 'Unauthorized action.');
            }
        }

        if ($query->$column == Status::ENABLE) {
            $query->$column = Status::DISABLE;
        } else {
            $query->$column = Status::ENABLE;
        }
        $message       = keyToTitle($column). ' changed successfully';

        $query->save();
        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }


    public function statusBadge(): Attribute
    {
        return new Attribute(
            get: fn () => $this->badgeData(),
        );
    }

    public function badgeData()
    {
        $html = '';
        if ($this->status == Status::ENABLE) {
            $html = '<span class="badge badge--success">' . trans('Enabled') . '</span>';
        } else {
            $html = '<span class="badge badge--warning">' . trans('Disabled') . '</span>';
        }
        return $html;
    }

    public function scopeActive($query)
    {
        return $query->where('status', Status::ENABLE);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', Status::DISABLE);
    }
}
