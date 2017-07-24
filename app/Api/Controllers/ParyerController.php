<?php
/**
 * Created by PhpStorm.
 * User: si
 * Date: 2017/7/12
 * Time: 23:34
 */

namespace App\Api\Controllers;


use App\Api\Server\UserServer;
use App\Api\Transformers\PayerTransformer;
use App\Payer;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Validator;

class ParyerController extends BaseController
{

    public function addPayer()
    {
//      //  dd('123');
//        $rules = [
//            'name' => 'required',
//            'idCard' => 'required|unique:payers,idCard',
//        ];
//        $payload = app('request')->only('name', 'idCard');
//        $messages = [
//            'idCard.unique' =>'身份证号已经存在,请重新输入'];
//        $validator =   Validator::make($payload, $rules,$messages);
//
////
//        if (!$validator->passes()) {
//            return $validator->errors();
//        }
        $postData = request(['name','idCard']);
        $user = $this->getAuthenticatedUser();
        $payer=Payer::where(['user_id'=>$user->id,'idCard'=>request('idCard')])->first();
        if ($payer) {
            return response()->json([
                'status' => false,
                'message' => '此用户已经存在,不需要重复添加'
            ], 404);
        }
        $payer = new Payer();
        $payer->create(array_merge($postData,['user_id'=>$user->id]));
        return response()->json([
            'status' => true,
            'message' => '增加成功,去购票'
        ], 200);
    }
    public function getAuthenticatedUser()
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

    public function payerList(){
        $user = UserServer::getUser();
        $user_id = $user->id;
        $payer =  Payer::where('user_id','=',$user_id)->get();

        if (!$payer->isEmpty()) {
            return $this->collection($payer,new PayerTransformer());
        }
        return null;
    }



}