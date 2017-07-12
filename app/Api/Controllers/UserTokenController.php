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
        $token = $ut->getToken();
        return $token;
     }

    
}