<?php

namespace App\Http\Controllers;

use App\Ticket;
use app\Wechat\WxPayApi;
use app\Wechat\WxPayJsApiPay;
use app\Wechat\WxPayUnifiedOrder;
use Illuminate\Http\Request;


class WeChatController extends Controller
{
    public $id ='';

    public function qiuniu()
    {
        $t = Ticket::where('tno' ,'A722947197904494')->first();
        $t->token = '1111' ;

        $t->save();

    }
    public function index(Request $request)
    {
        $no = $request->no;
        $this->id = $no;
        if ($no){

            $wxOrderData  = new WxPayUnifiedOrder();
            $wxOrderData->SetOut_trade_no($no);
            $wxOrderData->SetTrade_type("JSAPI");
            /*        $wxOrderData->SetTotal_fee((string)$totalfee);*/
            $wxOrderData->SetTotal_fee(1);
            $wxOrderData->SetBody('商丘');
            $wxOrderData->SetOpenid('oZaLq0EEFIVm7fQTYH6z6awldj0U');
            $wxOrderData->SetNotify_url('https://www.numbersi.cn/api/notifyUrl');

            return $this->getPaySignature($wxOrderData);


        }
    }

    public function getPaySignature($wxOrderData)
    {
        $wxOrder =WxPayApi::unifiedOrder($wxOrderData);
        if ($wxOrder['return_code'] != 'SUCCESS' ||
            $wxOrder['result_code'] != 'SUCCESS'
        )
        {
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
        $jsApiPayData->SetPackage('prepay_id='.$wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');
        $sign = $jsApiPayData->MakeSign();
        $rawData = $jsApiPayData->GetValues();
        $this->recordPreOrder($wxOrder['prepay_id']);
        $rawData['paySign']= $sign;
        return $rawData;
    }
    private function recordPreOrder($prepay_id)
    {
        $t = Ticket::where('tno' ,$this->id)->first();
        $t->prepay_id = $prepay_id;
        $t->save();
    }

}
