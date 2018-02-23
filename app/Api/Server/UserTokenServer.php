<?php
namespace App\Api\Server;

/**
 * Created by PhpStorm.
 * User: si
 * Date: 2017/7/11
 * Time: 18:25
 */
use App\common;
use App\Openid;
use App\Payer;
use App\User;
use Illuminate\Support\Facades\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;

class UserTokenServer
{
    protected $code;
    protected $wxAppID ='' ;
    protected $wxAppSecret='' ;
    protected $wxLoginUrl='';
    protected $name;
    protected $avatarUrl;
    function __construct($code=9,$name='',$avatarUrl='')
    {
        $this->name = $name;
        $this->avatarUrl = $avatarUrl;
        $this->code = $code;
        $this->wxAppID = env('wxAppID');
        $this->wxAppSecret =env('wxAppSecret');
        $this->wxLoginUrl = sprintf(
            env('wxLoginUrl'),
            $this->wxAppID, $this->wxAppSecret, $this->code);

    }

    public function getToken()
    {
        $result =common::curl_get($this->wxLoginUrl);

        //https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=APPID&secret=APPSECRET
//        https://api.weixin.qq.com/cgi-bin/user/info?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN
//        $uerinfo_url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=7_9GbAY4u0aLcskmtN4EPD_0xfkvVLrxmaol7Ty3-4nupwsKB3TOFECrtFcHA4JWurso-chdqHhks179pXk1M7PHUEpS04mEaoGbsjPfNGlBu-olvB2_BKmrTByyuyMRqiHs2ADTNMvcPttDq-BKLiAEAJST&openid=$10$BLki484vSnTNslIBUiBkXO/YycPda5rR7f6c9KLZ6E732tcXVG7ki&lang=zh_CN';
//        $result =common::curl_get($uerinfo_url);

       // $request = Request::create($this->wxLoginUrl, 'GET');
        //dd($request->appid);

        /*
          $wxResult = json_decode($result, true);
        if (empty($wxResult))
         */
        $wxResult = json_decode($result, true);
        if (empty($wxResult)) {
            return '获取session_key及openID时异常，微信内部错误';
        }else{
            if (array_key_exists('errcode',$wxResult)) {
                return response()->json(['error' =>$wxResult['errcode'] , 500]);
            } else {
                return $this->grantToken($wxResult);
            }
        }
    }

    private function grantToken($wxResult)
    {
       // $id = ['openid'=>$wxResult['openid']];
        $openid = $wxResult['openid'];
       // $OpenId = new Openid;
        //检验openid 是否存在, 是获取user,生成token 否就存入OpenID

        $user = User::where('openid',$openid)->first();

        if($user){

        }else{
            //增加 openid 微信用户
            $params = [
                'openid' => $openid,
                'name' =>  $this->name,
                'avatarUrl' =>  $this->avatarUrl,
                'email' => 'demo@demo.demo',
                'password' =>bcrypt('demo'),
                'remember_token' => 'remember_token',
            ];
             User::create($params)->getCoupon()->attach(1);

        }
       // dd($user);
        try {
            // attempt to verify the credentials and create a token for the user
            // dd(JWTAuth::attempt($credentials));
            if (!$user) {
                return '获取wx用户失败';
            }
            $token = JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json(compact('token'));

      //  return $s;

    }

};