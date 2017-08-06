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
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Symfony\Component\Console\Helper\Table;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Validator;

class ParyerController extends BaseController
{
    protected  $pay_id;
    public function addPayer()
    {
        $postData = request(['name','idCard']);
        $user = $this->getAuthenticatedUser();
        $payer=Payer::where($postData)->first();

        if ($payer) {
            if ($user->payers()->get()->contains($payer)) {
                            return [
                'status' => false,
                'message' => '此用户已经存在,不需要重复添加'
            ];
            }else{
                $user->payers()->attach($payer);
                return [
                    'status' => true,
                    'message' => '增加成功,去购票'
                ];
            }
        }
        $payer = new Payer();
        $payer = $payer->create($postData);
        $user->payers()->attach($payer);
        return [
            'status' => true,
            'message' => '增加成功,去购票'
        ];
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
        $payer = $user->payers()->get();
        if (!$payer->isEmpty()) {
            return $this->collection($payer,new PayerTransformer());
        }
        return null;
    }
}