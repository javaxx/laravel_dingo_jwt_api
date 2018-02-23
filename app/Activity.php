<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Activity extends Model
{
    //
    protected $guarded = [];

    public function coupon()
    {
        return $this->belongsTo(\App\Coupon::class, 'coupon_id', 'id');
    }
}
