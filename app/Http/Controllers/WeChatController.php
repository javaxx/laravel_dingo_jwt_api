<?php

namespace App\Http\Controllers;

use app\Wechat\WxPayApi;
use app\Wechat\WxPayJsApiPay;
use app\Wechat\WxPayUnifiedOrder;
use EasyWeChat\Payment\Business;
use EasyWeChat\Payment\UnifiedOrder;
use Illuminate\Http\Request;



class WeChatController extends Controller
{

    public function index(Request $request)
    {
        $no = $request->no;
        if ($no){

            $wxOrderData  = new WxPayUnifiedOrder();
            $wxOrderData->SetOut_trade_no($no);
            $wxOrderData->SetTrade_type("JSAPI");
            /*        $wxOrderData->SetTotal_fee((string)$totalfee);*/
            $wxOrderData->SetTotal_fee(1);
            $wxOrderData->SetBody('NumberSi_body');
            $wxOrderData->SetOpenid('oZaLq0EEFIVm7fQTYH6z6awldj0U');
            $wxOrderData->SetNotify_url('http://www.baidu.com');
            $wxOrder =WxPayApi::unifiedOrder($wxOrderData);
            $signature = $this->sign($wxOrder);
            return $signature;
        }



//        dd($signature);
//
//        dd($wxOrder);
//        dd($wxOrderData);

    }


    private function sign($wxOrder)
    {

        //  var_dump($wxOrder);

        $jsApiPayData = new WxPayJsApiPay();
        $jsApiPayData->SetAppid(env('wxAppID'));
        $jsApiPayData->SetTimeStamp((string)time());
        $rand = md5(time() . mt_rand(0, 11111));
        $jsApiPayData->SetNonceStr($rand);
        $jsApiPayData->SetPackage('prepay_id='.$wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');
        $sign = $jsApiPayData->MakeSign();
        $rawData = $jsApiPayData->GetValues();

        $rawData['paySign']= $sign;
        return $rawData;
    }

}
