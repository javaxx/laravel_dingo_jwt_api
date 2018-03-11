<?php
/**
 * Created by PhpStorm.
 * User: si
 * Date: 2017/7/11
 * Time: 17:59
 */

namespace App\Api\Controllers;


use App\Api\Server\UserTokenServer;
use App\Sharelist;
use App\User;
use app\Wechat\WXBizDataCrypt;
use Auth;
use Illuminate\Http\Request;


class UserTokenController extends BaseController
{
    public function index(Request $request)
    {
        $data = $request->only(['code', 'name', 'avatarUrl']);
        $ut = new UserTokenServer($data['code'], $data['name'], $data['avatarUrl']);
//        $token = $ut->getToken();
        return $ut->getToken();
    }

    public function getUser(Request $request)
    {
        $user = Auth::user();
        $getUser = User::find($request->id);
        if ($getUser == $user || $getUser == null) {
            $followers = $user->getFollower;
            $new_num = $user->getFollowerByStatus_0->count();
            return ['user' => $user,
                'followers' => $followers,
                'new_num' => $new_num,
                'self' => true
            ];
        } else {
            $followers = $getUser->getFollower;
            $new_num = $getUser->getFollowerByStatus_0->count();
            return [
                'leader' => $getUser,
                'new_num' => $new_num,
                'followers' => $followers,
                'my_leader' => $user->getLeader,
                'user' => $user,
                'isFollower' => Sharelist::isFollower($user->id),
                'self' => false
            ];
        }

    }

    public function getPhone(Request $request)
    {
        $user = Auth::user();
        if (!$user->phoneNumber) {
            $iv = $request->iv;
            $encryptedData = $request->encryptedData;
            $code = $request->code;
            $ut = new UserTokenServer($code);
            $sessionKey = $ut->getSessionKey();

            $pc = new WXBizDataCrypt($ut->wxAppID, $sessionKey);
            $errCode = $pc->decryptData($encryptedData, $iv, $data);

            if ($errCode == 0) {

                $user->phoneNumber =  $data->phoneNumber;
                $user->save();

                return response()->json(['status' => true, 'phoneNumber' => $data->phoneNumber,]);
            } else {
                return response()->json(['status' => true, 'errCode' => $errCode]);
            }
        }
        return response()->json(['status' => true, 'phoneNumber' => $user->phoneNumber,]);


    }
}