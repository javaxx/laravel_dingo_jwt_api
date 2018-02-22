<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sharelist extends Model
{
    protected $table = 'sharelist';
    public static function isFollower($userID)
    {
        $r = self::where(['follow_uid' => $userID,])->get();
        if ($r->isEmpty()) {
            return false;
        }
        return true;
    }
}
