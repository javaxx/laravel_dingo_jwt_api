<?php
namespace App\Api\Server;

/**
 * Created by PhpStorm.
 * User: si
 * Date: 2017/7/11
 * Time: 18:25
 */
use App\common;

class UserTokenServer
{
    protected $code;
    protected $wxAppID ='' ;
    protected $wxAppSecret='' ;
    protected $wxLoginUrl='';

    function __construct($code=9)
    {

        $this->code = $code['code'];
        $this->wxAppID = env('wxAppID');
        $this->wxAppSecret =env('wxAppSecret');
        $this->wxLoginUrl = $code;
        $this->wxLoginUrl = sprintf(
            env('wxLoginUrl'),
            $this->wxAppID, $this->wxAppSecret, $this->code);
//        $this->wxLoginUrl = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $this->wxAppID . '&secret=' . $this->wxAppSecret . '&js_code='.'.$code';
       // $a = "https://api.weixin.qq.com/sns/jscode2session?appid=$this->wxAppID &secret= $this->wxAppSecret";// &js_code=$this->code&grant_type=authorization_code";
       // $this->wxLoginUrl = $a." &js_code=$this->code&grant_type=authorization_code";

    }

    public function getWxinfo()
    {

        //dd($this);
        $s =common::curl_get($this->wxLoginUrl);


        return $s;

    }

};