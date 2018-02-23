<?php

namespace App\Api\Controllers;

use App\Coupon;
use App\Sharelist;
use App\User;
use function GuzzleHttp\Psr7\str;
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
        $coupon_id = 1;
        $num = $newFollower->count();
        $num = (int)floor($num / 5);
        $delFollowerMum = $num * 5;
        while ($num > 0) {
            $user->getCoupon()->attach($coupon_id);
            $num--;
        }

        $a = Sharelist::where(['share_uid' => $user->id, 'status' => 0])->skip(0)->take($delFollowerMum)->update(['status' => 1]);
        $c = Coupon::find($coupon_id);
        if ($a) {
            $num = (int)floor($a / 5);

            return ['msg' => '恭喜你获取' .intval($num) . '张' .$c->description,
            ];
        }
    }

    public function follow(Request $request)
    {
        $user = Auth::user();
        $isFollowd = Sharelist::where(['share_uid'=>$user->id, 'follow_uid' => $request->leader_id])->get();
        $r =  $this->isFolloed($user->id, $request->leader_id);

        if ($r) {
            return ['status' => false,
                'msg' => '不能加入队员的队伍',
            ];
        }
        $newFollow = new Sharelist();
        $newFollow->share_uid = $request->leader_id;
        $newFollow->follow_uid = $user->id;
        $newFollow->save();
        return ['status' => true,
            'msg' => '加入成功',];
    }

    public function isFolloed( $folloow_id,$share_uid)
    {
      $r =   Sharelist::where(['share_uid'=>$folloow_id ,'follow_uid' =>$share_uid])->get();

        if ($r->count()) {
            return true;
        }
        return false;
    }

}
