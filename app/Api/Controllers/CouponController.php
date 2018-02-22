<?php

namespace App\Api\Controllers;

use App\Sharelist;
use App\User;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\App;

class CouponController extends BaseController
{
    //
    public function addCoupon()
    {
        $user = Auth::user();
        $newFollower = $user->getFollowerByStatus_0;
        $num =$newFollower->count();
        $num = (int)floor($num / 5);
        $delFollowerMum = $num * 5;

        while ($num > 0) {
           $user->getCoupon()->attach(1);
           $num--;
        }
        //Sharelist::where(['status'=>l])
        $a = Sharelist::where(['share_uid' => $user->id, 'status' => 0])->skip(0)->take($delFollowerMum)->update(['status' => 1]);
//        $a = Sharelist::where(['share_uid' => $user->id, 'status' => 0])->limit($delFollowerMum)->update(['status' => 1]);
//        dd($newFollower);

     //   dd();
    }

    public function follow(Request $request)
    {
        $user = Auth::user();
        $newFollow = new Sharelist();
        $newFollow->share_uid = $request->leader_id;
        $newFollow->follow_uid = $user->id;
        $newFollow->save();
    }

}
