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
use Auth;
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
        $user = Auth::user();
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
        $payer = $payer->create(array_add($postData,'user_id',Auth::id()));
        $user->payers()->attach($payer);
        return [
            'status' => true,
            'message' => '增加成功,去购票'
        ];
    }
    public function payerList(){
        $user = Auth::user();
        $payer = $user->payers()->get();
       // dd($this->collection($payer, new PayerTransformer()));
        if (!$payer->isEmpty()) {
            return ['payers'=>$payer,'coupons'=>$user->getCoupon()->get()];
        }
        return null;
    }
}