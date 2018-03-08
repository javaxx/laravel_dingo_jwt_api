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
    public $wxAppID ='' ;
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

    public function getSessionKey(){
        $result =common::curl_get($this->wxLoginUrl);
        $wxResult = json_decode($result, true);
        if (empty($wxResult)) {
            return '获取session_key及openID时异常，微信内部错误';
        }else{
            if (array_key_exists('errcode',$wxResult)) {
                return response()->json(['error' =>$wxResult['errcode'] , 500]);
            } else {
                return $wxResult['session_key'];
            }
        }
    }
    public function getToken()
    {
        $result =common::curl_get($this->wxLoginUrl);

        $wxResult = json_decode($result, true);
        if (empty($wxResult)) {
            return response()->json(['msg' =>'获取session_key及openID时异常，微信内部错误' , 500]);

        }else{
            if (array_key_exists('errcode',$wxResult)) {
                return response()->json(['error' =>$wxResult['errcode'],'msg'=>$wxResult['errmsg']],500);
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
        return response()->json(['token' => $token,'msg'=>'登陆成功'],200,['asd'=>'asd']);
    }

};