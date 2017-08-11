<?php

namespace App\Http\Controllers;

use App\AdminRole;
use App\Api\Server\AccessTokenServer;
use App\common;
use App\Payer;
use App\Ticket;
use App\User;
use app\Wechat\WxPayApi;
use app\Wechat\WxPayJsApiPay;
use app\Wechat\WxPayUnifiedOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class WeChatController extends Controller
{
    public $id ='';

    public function qiuniu()
    {
        $token = (new AccessTokenServer)->getToken();
        $url = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=' . $token;
        $params = ['scene' =>'abc', 'page' => 'pages/home/home',
            'width' => 250,

        ];
        $picturedata = common::curl_post($url, $params);
        $disk = \Storage::disk('qiniu');
       $a =  $disk->put('123'.'.png',$picturedata);
    }
    public function index(Request $request)
    {
        $no = $request->no;
        $this->id = $no;
        if ($no){
            $wxOrderData  = new WxPayUnifiedOrder();
            $wxOrderData->SetOut_trade_no($no);
            $wxOrderData->SetTrade_type("JSAPI");
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
