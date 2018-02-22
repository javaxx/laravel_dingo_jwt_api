<?php

namespace App\Http\Controllers;

use App\Ticket;
use App\User;
use app\Wechat\WxPayApi;
use app\Wechat\WxPayJsApiPay;
use app\Wechat\WxPayRefund;
use app\Wechat\WxPayUnifiedOrder;
use Illuminate\Http\Request;

class WeChatController extends Controller
{
    public $id ='';

    public function aaa(User $user)
    {
    }
    public function qiuniu()
    {
/*        $token = (new AccessTokenServer)->getToken();
        $url = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=' . $token;
        $params = ['scene' =>'abc', 'page' => 'pages/home/home',
            'width' => 250,

        ];
        $picturedata = common::curl_post($url, $params);
        $disk = \Storage::disk('qiniu');
       $a =  $disk->put('123'.'.png',$picturedata);*/


        // $path = public_path('qrcodes/' . $filesName . '.png');
/*        $picturedata=  QrCode::format('png')->size(250)->margin(1)->merge('/public/qrcodes/icon.png',.15)->generate(123123);
        // $this->getImage($path);
        $disk = \Storage::disk('qiniu');
        $disk->put('123123.png',$picturedata);*/
       // Storage::disk('local')->put('file.txt','123');
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
        $t = Ticket::where('tno', $this->id)->first();
        $t->prepay_id = $prepay_id;
        $t->save();
    }

    public function refound()
    {
        $reFound = new  WxPayRefund();
        $wxappid           = '';//应用ID 字符串
        $mch_id            = '';//商户号 字符串
        $notify_url        = 'http://api.***.com/api/Wechatpay/apppaysucess';//接收微信支付异步通知回调地址 字符串
        $wxkey             = '';//这个是在商户中心设置的那个值用来生成签名时保证安全的 字符串
      //  $wechatAppPay = new wechatapppay($wxappid,$mch_id, $notify_url,$wxkey);
        $params                     = array();
//        $params['out_trade_no']     = $order_ns;            //必填项 自定义的订单号
//        $params['total_fee']        = ($details*100);       //必填项 订单金额 单位为分所以要*100
//        $params['return_oid']     = $return_oid;            //退款单号
        // openid    oZaLq0EEFIVm7fQTYH6z6awldj0U
        //  transaction_id   wx20180220000458b5c57a491b0808767972
        // out_trade_no     B220562973858294
        // out_refund_no    B220562973858222
        //缺少必填参数op_user_id
      //  $reFound->SetAppid($wxappid);
        $reFound->SetOut_trade_no('B220562973858294');
        $reFound->SetOut_refund_no('B220562973858222');
        $reFound->SetTotal_fee(1);
        $reFound->SetRefund_fee(1);
        $reFound->SetOp_user_id('oZaLq0EEFIVm7fQTYH6z6awldj0U');
        //dd($reFound);
        return $this->getRuFundSignature($reFound);
    }

    public function getRuFundSignature($reFound)
    {
        $r = WxPayApi::refund($reFound);
        return $r;

    }

}
