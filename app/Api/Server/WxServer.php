<?php
/**
 * Created by PhpStorm.
 * User: si
 * Date: 2017/7/21
 * Time: 20:04
 */

namespace App\Api\Server;


use App\common;
use App\Ticket;
use app\Wechat\WxPayNotify;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class WxServer extends WxPayNotify
{

    public function NotifyProcess($data, &$msg)
    {
        Storage::disk('local')->put('file1.txt',"支付完成调用了");

        if ($data['result_code'] == 'SUCCESS')
        {
            $tno = $data['out_trade_no'];
            $token = $this->getToken($tno);
            $t = Ticket::where(['tno'=>$tno,'token'=>''])->first();
            if ($t) {
                $t->update(['token'=>$token]);
                $this->getQrCode($tno,$token);
                $this->senMoMessage($t);
            }else{
                Storage::disk('local')->put('file.txt',' 没有 ');
            }

        }
        else
        {
            return true;
        }


    }

    public function getToken($tno){
        return bcrypt('NumberSi0102' . $tno);
    }

    public function getQrCode($tno,$token)
    {
        // $path = public_path('qrcodes/' . $filesName . '.png');
        $picturedata=  QrCode::format('png')->size(250)->margin(1)->merge('/public/qrcodes/bus.jpg',.15)->generate($token);
        // $this->getImage($path);
        $disk = \Storage::disk('qiniu');
        $disk->put($tno.'.png',$picturedata);

    }

    public function senMoMessage($t)
    {

        $accessTokenServer= new AccessTokenServer();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$accessTokenServer->token;
        $params = [
            'touser' => $t->users->openid,
            'template_id' => 'IXLD8bxIF_YMjQ2cnJW1oIVSjVCXVVl50goeJhLqnLw',
            'page' => 'pages/me/me',
            'form_id' => $t->prepay_id,
            "data" => [
                "keyword1" => [
                    "value" => "沙集客运微信票",
                    "color" => "#173177"
                ],
                "keyword2" => [
                    "value" => "123123",
                    "color" => "#173177"
                ],
                "keyword3" => [
                    "value" => "请近日乘车,以免过期",
                    "color" => "#173177"
                ],
                "keyword4" => [
                    "value" => "每天最晚8点发车",
                    "color" => "#173177"
                ],
                "keyword5" => [
                    "value" => "上车请出票,请勿让他人获取二维码",
                    "color" => "#173177"
                ],
                "keyword6" => [
                    "value" => "150",
                    "color" => "#173177"
                ],
                "keyword6" => [
                    "value" => "乘车旅途中如果遇到问题,请拨打13737028118",
                    "color" => "#173177"
                ],
                "keyword6" => [
                    "value" => "上车地点可以在小程序首页查看详细地图信息",
                    "color" => "#173177"
                ],
            ],

        ];

        common::curl_post($url,$params);
    }

}