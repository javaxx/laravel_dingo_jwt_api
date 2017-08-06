<?php
/**
 * Created by PhpStorm.
 * User: si
 * Date: 2017/7/19
 * Time: 17:49
 */

namespace App\Api\Controllers;


use App\Api\Server\UserServer;
use App\Api\Server\WxServer;
use App\Ticket;
use app\Wechat\WxPayApi;
use app\Wechat\WxPayJsApiPay;
use app\Wechat\WxPayUnifiedOrder;
use Illuminate\Http\Request;


class WeChatController extends BaseController
{

    public $id ='';

    public function qiuniu()
    {



    }
    public function index(Request $request)
    {
        $user= UserServer::getUser();
       $Openid=$user->openid;
        $no = $request->no;
        $this->id = $no;
        if ($no){
            $tc =new TicketController();
            $price = $tc->getPrice();
            $wxOrderData  = new WxPayUnifiedOrder();
            $wxOrderData->SetOut_trade_no($no);
            $wxOrderData->SetTrade_type("JSAPI");
            $wxOrderData->SetTotal_fee($price*100);
            $wxOrderData->SetBody('订购商丘-张家港车票');
            $wxOrderData->SetOpenid($Openid);
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
        $t = Ticket::where(['tno'=>$this->id])->first();
        $t->prepay_id = $prepay_id;
        $t->created_at=date('Y-m-d h:i:s');
        $t->save();
    }


public function notifyUrl(){

    $wxServer = new WxServer();

    $wxServer->Handle();


}
}