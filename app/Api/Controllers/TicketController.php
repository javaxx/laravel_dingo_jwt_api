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
use App\Coupon;
use App\Payer;
use App\Ticket;
use Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketController extends BaseController
{


    public function getNo(Request $request)
    {
        $tno = $this->getOrderNo();
        $payerID = $request->payerID;
        $couponID = $request->couponID;
        $payer = Payer::find($payerID);//
        if ($payer) {
            $user = Auth::user();
            Storage::disk('local')->put('file.txt', $user);
            $priceData = $this->preferential_price($couponID);

            Ticket::where(['user_id' => $user->id, 'token' => ''])->delete();
            $payer_id = $payerID;
            $params = [
                'token' => '',
                'tno' => $tno,
                'user_id' => $user->id,
                'payer_id' => $payer_id,
                'coupon_id' => $couponID,
                'money' => $priceData['money'],
                'price' => $priceData['price'],
            ];
            $t = Ticket::create($params);
            if ($t) {
                return response()->json([
                    'ticket' => $t,
                    'message' => '下单成功,请支付',
                    'status' => true,
                ], 222);
            }

        }

    }

    public function preferential_price($couponID)
    {
        $price = Config::getValues("TPrice")->values;
        if ($couponID == -1) {
            return ['price' => $price,
                'money' => $price,
                ];
        }
        $coupon = Coupon::find($couponID);
        if (!$coupon) {
            return ['price' => $price,
                'money' => $price,
            ];
        }
        return ['price' => $price,
            'money' => round( $price - $coupon->money,2),
        ];
    }

    /*
     *  根据 tno 查询单个订单 信息
     */
    public function getTicketByNO(Request $request)
    {
        $tno = $request->tno;
        $t = Ticket::where(['tno' => $tno])->with('payers')->get();
        if ($t->isEmpty()) {
            return ['status' => false, 'ticket' => $t];
        }
        return ['status' => true, 'ticket' => $t];
    }

    public function getPrice()
    {
        $price = Config::getValues("TPrice");
        return response()->json(['price' => $price->values], 200);
    }

    public function changePrice(Request $request)
    {
        if ($request->changePrice) {
            $price = Config::getValues("TPrice");
            $price->values = $request->changePrice;
            $price->save();
            return '修改成功,此刻票价是' . $request->changePrice;
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
        //-> orderBy('status', 'asc')
        /*
         * 1  未使用
         *      退票的
         *      过期的
         * 2 已使用
         *
         */


        $Ticket = Ticket::where(['user_id' => $user->id])->with('payers')->latest('created_at')->get()->reject(function ($item, $key) {
            $outTime = 2;
            $item->times = strtotime($item->created_at);
            $item->expiredTimes = $item->times - 86400 * $outTime;
            $item->outTime = $outTime;
            if ($item->token == '') {
                return $item;
            }
        });
        $checked_T = $Ticket->filter(function ($item) {
            return $item->status == 1;
        });
        $tickets = $Ticket->filter(function ($item) {
            return $item->status == 0;
        });
        $refund_tickets = $Ticket->filter(function ($item) {
            return $item->status == 2;
        });
        return [
            'status' => !$Ticket->isEmpty(),
            'ticketsCount' => $Ticket->count(),
            'checked_T' => $checked_T,
            'tickets'=>$tickets,
            'refund_tickets' => $refund_tickets,
            'roles' => $roles,
            'coupons' => $user->getCoupon
        ];
    }


    public function getNotPayTickets()
    {
        $id = Auth::id();
        $Ticket = Ticket::where(['user_id' => $id])->with('payers')->orderBy('status', 'asc')->latest('updated_at')->get()->reject(function ($item, $key) {
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
            $result = Ticket::destroy($id);

            if ($result > 0) {
                return ['status' => true, 'message' => '删除成功'];
            }
            return ['status' => false, 'message' => '删除失败'];
        }
        return ['status' => false, 'message' => '此订单不存在,请刷新'];

    }

}
