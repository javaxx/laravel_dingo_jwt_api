<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payer extends Model
{
    //
    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany('App\User','user_payer');
    }
}
