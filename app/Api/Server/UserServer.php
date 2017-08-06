<?php
/**
 * Created by PhpStorm.
 * User: si
 * Date: 2017/7/19
 * Time: 8:48
 */

namespace App\Api\Server;


use JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class UserServer
{

    public static function getUser()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {

                return response()->json(['user_not_found'], 404);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }

        return $user;
    }


}