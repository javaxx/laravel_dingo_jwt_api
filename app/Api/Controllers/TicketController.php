<?php
/**
 * Created by PhpStorm.
 * User: si
 * Date: 2017/7/18
 * Time: 12:51
 */

namespace App\Api\Controllers;


use App\Api\Server\UserServer;
use App\Ticket;

class TicketController
{


    public function getNo()
    {
        return $this->getOrderNo();


    }
    public function addTicket()
    {
        $orderNo = $this->getOrderNo();

        if ($orderNo) {

            if (!request(['no'])) {
                return $orderNo;
            }
            $orderNo = request(['no']);
            $payer_id = '4';
            $user = UserServer::getUser();
            $user_id = $user->id;
            $params = [
                'tno' => $orderNo,
                'user_id'=>$user_id,
                'payer_id' => $payer_id,
                'money' => 0,
            ];
            $t=Ticket::create($params);
            if ($t) {

            }
        }


    }

    public function getOrderNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
            $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }
    public function getToken(){
        $this->getOrderNo();
    }
}