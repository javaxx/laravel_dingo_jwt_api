<?php
/**
 * Created by PhpStorm.
 * User: si
 * Date: 2017/7/19
 * Time: 17:49
 */

namespace App\Api\Controllers;


use App\Api\Server\WxServer;
use App\Coupon;
use App\Ticket;
use app\Wechat\WxPayRefund;
use Auth;
use app\Wechat\WxPayApi;
use app\Wechat\WxPayJsApiPay;
use app\Wechat\WxPayUnifiedOrder;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Http\Request;


class WeChatController extends BaseController
{

    public $id = 0;
    public function index(Request $request)
    {
        $user = Auth::user();
        $Openid = $user->openid;
        $no = $request->no;
        $t = Ticket::where(['tno'=>$no])->with('payers')->first();

        if ($t) {
            $price = $t->money;
            $this->delUserCoupon($user,$t->coupon_id);
            $wxOrderData = new WxPayUnifiedOrder();
            $wxOrderData->SetOut_trade_no($no);
            $wxOrderData->SetTrade_type("JSAPI");
            $wxOrderData->SetTotal_fee($price * 100);
            $wxOrderData->SetBody('商丘-张家港车票');
            $wxOrderData->SetOpenid($Openid);
            $wxOrderData->SetNotify_url('https://t.numbersi.cn/api/notifyUrl');
            return $this->getPaySignature($wxOrderData,$t);
        }
    }


    public function getPaySignature($wxOrderData,$t)
    {
        $wxOrder = WxPayApi::unifiedOrder($wxOrderData);


        if ($wxOrder['return_code'] != 'SUCCESS' ||
            $wxOrder['result_code'] != 'SUCCESS'
        ) {
            return [];
        }
        $signature = $this->sign($wxOrder);

        $this->recordPreOrder($wxOrder['prepay_id'] , $t);


        return $signature;

    }

    private function sign($wxOrder)
    {
        $jsApiPayData = new WxPayJsApiPay();
        $jsApiPayData->SetAppid(env('wxAppID'));
        $jsApiPayData->SetTimeStamp((string)time());
        $rand = md5(time() . mt_rand(0, 11111));
        $jsApiPayData->SetNonceStr($rand);
        $jsApiPayData->SetPackage('prepay_id=' . $wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');
        $sign = $jsApiPayData->MakeSign();
        $rawData = $jsApiPayData->GetValues();
        $rawData['paySign'] = $sign;
        return $rawData;
    }

    private function recordPreOrder($prepay_id,$t)
    {
        $t->update(['prepay_id' => $prepay_id,
            'created_at'=>date('Y-m-d H:i:s')
            ]);
    }


    public function notifyUrl()
    {

        $wxServer = new WxServer();

        $wxServer->Handle();


    }

// 退款
    public function refund(Request $request)
    {

        $user = Auth::user();
        $Openid = $user->openid;
        $out_trade_no = $request->tno;

        if ($out_trade_no) {
            $t = Ticket::where(['tno'=>$out_trade_no])->first();
            if (!$t) {

                return [
                    'status' => false,
                    'msg' => '所查询的订单不存在',
                ];
            }
            if($t->status != 0){
                return [
                    'status' => false,
                    'msg' => '所查询的订单不可以退款',
                ];
            };
            $tPrice = $t->money*100;
        }
        $refund_fee = $tPrice;
        if ($request->outTime){
            $refund_fee = round(0.9 * $tPrice,2) ;
        }
        $ut_refund_no = 'ABC'.time();
        $reFound = new  WxPayRefund();
        $reFound->SetOut_trade_no($out_trade_no);
        $reFound->SetOut_refund_no($ut_refund_no);
        $reFound->SetTotal_fee($tPrice);
        $reFound->SetRefund_fee($refund_fee);
        $reFound->SetOp_user_id($Openid);
       return  $this->getRuFundSignature($reFound,$t);
    }
    public function getRuFundSignature($reFound,$t)
    {
//        $r = WxPayApi::refund($reFound);
        $r= $t->update(['status' => 0]);
        $r = [];
        if ($r['return_code'] != 'SUCCESS' ||
            $r['result_code'] != 'SUCCESS'
        ) {

            return [
                'status' => false,
                'msg' => $r['err_code_des'],
            ];
        }else{
            $r = $t->update(['status' => 2, 'update_at' => date('Y-m-d H:i:s'),
            ]);
            if ($r) {
                return ['status'=>true,'msg'=>'退票成功'];
            }
        }
    }


    public function delUserCoupon($user,$coupon_id)
    {

        if ($coupon_id == -1) {
            return;

        }
        $user->getCoupon()->detach($coupon_id);
    }

}