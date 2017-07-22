<?php
/**
 * Created by PhpStorm.
 * User: si
 * Date: 2017/7/19
 * Time: 17:49
 */

namespace App\Api\Controllers;


use App\Api\Server\WxServer;

class WeChatController extends BaseController
{
public function index(){

    $wxServer = new WxServer();

    $wxServer->Handle();


}
}