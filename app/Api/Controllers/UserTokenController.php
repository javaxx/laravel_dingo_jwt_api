<?php
/**
 * Created by PhpStorm.
 * User: si
 * Date: 2017/7/11
 * Time: 17:59
 */

namespace App\Api\Controllers;


use App\Api\Server\UserTokenServer;
use App\Openid;
use App\Sharelist;
use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Auth;


class UserTokenController extends BaseController

{

    public function index(Request $request)
    {
        $data = $request->only(['code', 'name', 'avatarUrl']);
        $ut = new UserTokenServer($data['code'], $data['name'], $data['avatarUrl']);
        $token = $ut->getToken();
        return $token;
    }

    public function getUser(Request $request)
    {
        $user = Auth::user();
        if ($request->id) {
            $getUser = User::find($request->id);
            $self = $user == $getUser ? true : false;
        }else{
            $self = true;
        }
        if ($self){
            $followers = $user->getFollower;
            $new_num  = $user->getFollowerByStatus_0->count();
            return ['user' => $user,
                'followers' => $followers,
                'new_num' => $new_num,
                'self'=>$self
                ];
        }else{

            $followers = $getUser->getFollower;
            $new_num  = $getUser->getFollowerByStatus_0->count();

            return [
                'leader' => $getUser,
                'new_num' => $new_num,
                'followers' => $followers,
                'my_leader' => $user->getLeader,
                'user' =>$user,
                'isFollower' =>Sharelist::isFollower($user->id),
                'self'=>$self
            ];
        }
    }

    public function NotSelf()
    {

    }


}