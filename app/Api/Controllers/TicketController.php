<?php
/**
 * Created by PhpStorm.
 * User: si
 * Date: 2017/7/18
 * Time: 12:51
 */

namespace App\Api\Controllers;


use App\Api\Server\UserServer;
use App\Payer;
use App\Ticket;
use function GuzzleHttp\Promise\all;
use Illuminate\Http\Request;
use Qiniu\Auth;

class TicketController
{


    public function getNo(Request $request)

    {
        $tno = $this->getOrderNo();
        $payerID = $request->payerID;
        $payer = Payer::find($payerID);//
//        $payer = Payer::where(['id'=>$payerID])->find();//
        if ($payer) {
            $user = UserServer::getUser();
            $user_id = $user->id;

           $ts= Ticket::where(['user_id'=>$user_id,'token'=>''])->get();

            if ($ts->count()<5) {
                $payer_id = $payerID;
                $params = [
                    'token'=>'',
                    'tno' => $tno,
                    'user_id'=>$user_id,
                    'payer_id' => $payer_id,
                    'money' => $this->getPrice(),
                ];
                $t=Ticket::create($params);
                if ($t) {
                    return response()->json([
                        'no'=>$tno,
                        'message' => '下单成功,请支付',
                        'status' => true,
                    ], 222);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => '已超5个订单没有支付,请处理'
                ], 404);
            }
        }
    }

    public function getPrice()
    {
        return '150.00';
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


    public function getTicketList()
    {
        $id =\Illuminate\Support\Facades\Auth::id();

        return Ticket::where(['user_id'=> $id,'token'=>''])->with('payers')-> orderBy('status', 'asc')->latest('updated_at')->get();


}