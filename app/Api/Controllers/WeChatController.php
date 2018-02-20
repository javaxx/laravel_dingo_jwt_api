<?php
/**
 * Created by PhpStorm.
 * User: si
 * Date: 2017/7/19
 * Time: 17:49
 */

namespace App\Api\Controllers;


use App\Api\Server\WxServer;
use App\Ticket;
use Auth;
use app\Wechat\WxPayApi;
use app\Wechat\WxPayJsApiPay;
use app\Wechat\WxPayUnifiedOrder;
use Illuminate\Http\Request;


class WeChatController extends BaseController
{


    public function index(Request $request)
    {
        $user = Auth::user();
        $Openid = $user->openid;
        $no = $request->no;
        if ($no) {
            $tc = new TicketController();
            $price = $tc->getPrice();
            if ($user->name == 'AdminSi') {
                $price = 0.01;
            }
            $wxOrderData = new WxPayUnifiedOrder();
            $wxOrderData->SetOut_trade_no($no);
            $wxOrderData->SetTrade_type("JSAPI");
            $wxOrderData->SetTotal_fee($price * 100);
            $wxOrderData->SetBody('订购商丘-张家港车票');
            $wxOrderData->SetOpenid($Openid);
            $wxOrderData->SetNotify_url('https://t.numbersi.cn/api/notifyUrl');

            return $this->getPaySignature($wxOrderData);


        }
    }

    public function getPaySignature($wxOrderData)
    {
        $wxOrder = WxPayApi::unifiedOrder($wxOrderData);
        if ($wxOrder['return_code'] != 'SUCCESS' ||
            $wxOrder['result_code'] != 'SUCCESS'
        ) {
            Log::record($wxOrder, 'error');
            Log::record('获取预支付订单失败', 'error');
        }
        //prepay_id
        $signature = $this->sign($wxOrder);
        return $signature;

    }

    private function sign($wxOrder)
    {

        //  var_dump($wxOrder);

        $jsApiPayData = new WxPayJsApiPay();
        $jsApiPayData->SetAppid(env('wxAppID'));
        $jsApiPayData->SetTimeStamp((string)time());
        $rand = md5(time() . mt_rand(0, 11111));
        $jsApiPayData->SetNonceStr($rand);
        $jsApiPayData->SetPackage('prepay_id=' . $wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');
        $sign = $jsApiPayData->MakeSign();
        $rawData = $jsApiPayData->GetValues();
        $this->recordPreOrder($wxOrder['prepay_id']);
        $rawData['paySign'] = $sign;
        return $rawData;
    }

    private function recordPreOrder($prepay_id)
    {
        $t = Ticket::where(['tno' => $this->id])->first();
        $t->prepay_id = $prepay_id;
        $t->created_at = date('Y-m-d H:i:s');
        $t->save();
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
                return '所查询的订单不存在';
            }
            if($t->status != 0){
                return '所查询的订单不可以退款';
            };
            $tPrice = $t->money*100;
        }

        $ut_refund_no = 'ABC'.time();
        $reFound = new  WxPayRefund();
        $reFound->SetOut_trade_no($out_trade_no);
        $reFound->SetOut_refund_no($ut_refund_no);
        $reFound->SetTotal_fee($tPrice);
        $reFound->SetRefund_fee($tPrice*0.9);
        $reFound->SetOp_user_id($Openid);
        //dd($reFound);
        return $this->getRuFundSignature($reFound);
    }

    public function getRuFundSignature($reFound)
    {
        $r = WxPayApi::refund($reFound);
        dd($r);
    }


}