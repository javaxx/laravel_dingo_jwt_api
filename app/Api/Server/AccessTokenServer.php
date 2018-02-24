<?php
/**
 * Created by PhpStorm.
 * User: si
 * Date: 2017/8/10
 * Time: 16:21
 */

namespace App\Api\Server;


use App\common;
use Illuminate\Support\Facades\Storage;

class AccessTokenServer
{
    protected $access_token_url ;
    public $token;
     function __construct()
    {
        $this->access_token_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . env('wxAppID') . '&secret=' . env('wxAppSecret');
        $this->token = $this->getFilesToken();
    }
    /**
     * @return string
     */
    public  function  getToken()
    {
        return $this->getFilesToken();

    }
    public function getFilesToken()
    {
        if (Storage::exists('AccessToken.txt')) {
           $token = Storage::get('AccessToken.txt');
            $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$token;
            $params = [
                "touser" => "oZQ1aLq0EEFIVm7fQTYH6z6awldj0U",
                "msgtype" => "text",
                "text" => [
                "content" => "Hello World"]
            ];
            $request= common::curl_post($url, $params);
            $request = json_decode($request,true);
            Storage::disk('local')->put('request.txt', $request);

            if ($request['errcode'] == '40001'|| $request['errcode'] =="42001") {
                return $this->getUrlToken();
            }else{

                return $token;
            }
        }else{
            return $this->getUrlToken();
        }

    }
    public function getUrlToken()
    {
        $client =  new \GuzzleHttp\Client();
        $r =$client->request('get', $this->access_token_url,[]);
        $request =$r->getBody();
        $request = json_decode($request,true);

        Storage::disk('local')->put('AccessToken.txt', $request['access_token']);
        Storage::disk('local')->put('1.txt', $request['access_token']);

        return $request['access_token'];
    }
}