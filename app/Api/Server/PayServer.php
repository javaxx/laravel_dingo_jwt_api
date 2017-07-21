<?php
/**
 * Created by PhpStorm.
 * User: si
 * Date: 2017/7/19
 * Time: 17:23
 */

namespace App\Api\Server;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Auth,Redirect;
use Validator;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;


class PayServer
{
    protected function options(){ //选项设置
        return [
            // 前面的appid什么的也得保留哦
            'app_id' => 'xxxxxxxxx', //你的APPID
            'secret'  => 'xxxxxxxxx',     // AppSecret
            // 'token'   => 'your-token',          // Token
            // 'aes_key' => '',                    // EncodingAESKey，安全模式下请一定要填写！！！
            // ...
            // payment
            'payment' => [
                'merchant_id'        => '你的商户ID，MCH_ID',
                'key'                => '你的KEY',
                // 'cert_path'          => 'path/to/your/cert.pem', // XXX: 绝对路径！！！！
                // 'key_path'           => 'path/to/your/key',      // XXX: 绝对路径！！！！
                'notify_url'         => '你的回调地址',       // 你也可以在下单时单独设置来想覆盖它
                // 'device_info'     => '013467007045764',
                // 'sub_app_id'      => '',
                // 'sub_merchant_id' => '',
                // ...
            ],
        ];
    }
//传入订单ID即可，我这里是通过订单，来查询该订单的金额，当然你也可以自定义金额
    public function pay(){
        $id = Input::get('order_id');//传入订单ID
        $order_find = ExampleOrder::find($id); //找到该订单
        $mch_id = xxxxxxx;//你的MCH_ID
        $options = $this->options();
        $app = new Application($options);
        $payment = $app->payment;
        $out_trade_no = $mch_id.date("YmdHis"); //拼一下订单号
        $attributes = [
            'trade_type'       => 'APP', // JSAPI，NATIVE，APP...
            'body'             => '购买CSDN产品',
            'detail'           => $order_find->info, //我这里是通过订单找到商品详情，你也可以自定义
            'out_trade_no'     => $out_trade_no,
            'total_fee'        => $order_find->money*100, //因为是以分为单位，所以订单里面的金额乘以100
            // 'notify_url'       => 'http://xxx.com/order-notify', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            // 'openid'           => '当前用户的 openid', // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
            // ...
        ];
        $order = new Order($attributes);
        $result = $payment->prepare($order);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $order_find->out_trade_no = $out_trade_no; //在这里更新订单的支付ID
            $order_find->save();
            // return response()->json(['result'=>$result]);
            $prepayId = $result->prepay_id;
            $config = $payment->configForAppPayment($prepayId);
            return response()->json($config);
        }

    }
//下面是回调函数
    public function paySuccess(){
        $options = $this->options();
        $app = new Application($options);
        $response = $app->payment->handleNotify(function($notify, $successful){
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = ExampleOrder::where('out_trade_no',$notify->out_trade_no)->first();
            if (count($order) == 0) { // 如果订单不存在
                return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }
            // 如果订单存在
            // 检查订单是否已经更新过支付状态
            if ($order->pay_time) { // 假设订单字段“支付时间”不为空代表已经支付
                return true; // 已经支付成功了就不再更新了
            }
            // 用户是否支付成功
            if ($successful) {
                // 不是已经支付状态则修改为已经支付状态
                $order->pay_time = time(); // 更新支付时间为当前时间
                $order->status = 6; //支付成功,
            } else { // 用户支付失败
                $order->status = 2; //待付款
            }
            $order->save(); // 保存订单
            return true; // 返回处理完成
        });
    }

}