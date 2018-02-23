<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    //    protected $guarded = [];
    protected $guarded = [];

    public function payers()
    {
        return $this->belongsTo('App\Payer','payer_id','id');
    }
    public function users()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
}
