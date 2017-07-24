<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    //    protected $guarded = [];
    protected $guarded = [];
    protected $hidden=[
        'token','payer_id'
    ];
    public function payers()
    {
        return $this->belongsTo('App\Payer','payer_id','id');
    }
}
