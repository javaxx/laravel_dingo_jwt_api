<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $guarded = [];
    protected $hidden=[
        'token','payer_id'
    ];

    public static function getValues($name)
    {
        return self::where('name', $name)->first();

    }
}
