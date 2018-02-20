<?php
/**
 * Created by PhpStorm.
 * User: si
 * Date: 2017/7/18
 * Time: 12:51
 */

namespace App\Api\Controllers;


use App\Api\Server\UserServer;
use App\Config;
use App\Payer;
use App\Ticket;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketController extends BaseController
{


    public function getNo(Request $request)

    {
        $tno = $this->getOrderNo();
        $payerID = $request->payerID;
        $payer = Payer::find($payerID);//
        if ($payer) {

            $user = Auth::user();
            Storage::disk('local')->put('file.txt',$user);
            $price = $this->getPrice();

            if ($user->name==='AdminSi') {
                $price = 0.01;
            }
            $user_id = Auth::id();

           $ts= Ticket::where(['user_id'=>$user_id,'token'=>''])->get();

            if ($ts->count()<5) {
                $payer_id = $payerID;
                $params = [
                    'token'=>'',
                    'tno' => $tno,
                    'user_id' => $user_id,
                    'payer_id' => $payer_id,
                    'money' => $price,
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
        $price = Config::getValues("TPrice");

       return $price->values;
    }

    public function changePrice(Request $request){
        if ($request->changePrice) {
            $price = Config::getValues("TPrice");
            $price->values =$request->changePrice;
            $price->save();
            return '修改成功,此刻票价是'.$request->changePrice;
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


    public function getTicketList()
    {
        $user = Auth::user();
        $roles = $user->roles;
        $Ticket= Ticket::where(['user_id'=> $user->id])->with('payers')-> orderBy('status', 'asc')->latest('updated_at')->get()->reject(function ($item, $key) {
            if ($item->token == '') {
                return $item;
            }
        });
        if ($Ticket->isEmpty()) {
            return ['status' => false, 'tickets' => $Ticket,'roles'=>$roles];
        }
        return ['status' => true, 'tickets' => $Ticket,'roles'=>$roles];
    }


    public function getNotPayTickets()
    {
        $id =Auth::id();
        $Ticket = Ticket::where(['user_id'=> $id])->with('payers')-> orderBy('status', 'asc')->latest('updated_at')->get()->reject(function ($item, $key) {

            if ($item->token != '') {
                return $item;

            }
        });
        if ($Ticket->isEmpty()) {
            return ['status' => false, 'tickets' => $Ticket];
        }
        return ['status' => true, 'tickets' => $Ticket];
    }

    public function delTicket(Request $request)
    {



        $id = $request->id;
        if ($id) {
            $result =Ticket::destroy($id);

            if ($result>0) {
                return ['status'=>true,'message'=>'删除成功'];
            }
            return ['status'=>false,'message'=>'删除失败'];
        }
        return ['status'=>false,'message'=>'此订单不存在,请刷新'];

    }

}
