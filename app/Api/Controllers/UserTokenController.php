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
use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserTokenController extends BaseController

{

    public function index(Request $request)
    {
        $code = $request->only('code');

        $ut  = new UserTokenServer($code);
        $info = json_decode($ut->getWxinfo(),true);

/*        if (array_key_exists('errcode',$info)) {
            return '微信访问错误';
        }*/
      //  $id = ['openid'=>$info['openid']];
        $openid = ['id'=>123];
        $user = Openid::where('openid','123')->get();
        dd($user);
        try {
            // attempt to verify the credentials and create a token for the user
            // dd(JWTAuth::attempt($credentials));
            if ($user) {

            }
            $token = JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json(compact('token'));
     }

    
}