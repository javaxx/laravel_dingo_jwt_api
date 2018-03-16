<?php
/**
 * Created by PhpStorm.
 * User: si
 * Date: 2017/7/29
 * Time: 17:09
 */

namespace App\Api\Controllers;


use App\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckController extends BaseController
{


    public function getChecked(Request $request)
    {
        $date = $request->date;
        $tickets = Auth::user()->checkedTickets($date);
        $moneyCount = $tickets->sum('money');
        return ['status' => true, 'tickets' =>$tickets ,'moneyCount'=>$moneyCount];
    }
    public function checkTicket(Request $request)
    {
        $user = Auth::user();
        $token = $request->token;
        if ($token) {
            /*
             * 1 根据Token 获取 ticket
             *
             * 2 更新 ticket 的status 和 check_id
             *
             *   where(['token'=>$token])->
             */
            $tno = str_after(decrypt($token),'NumberSi0102');

            $ticket = Ticket::where(['tno'=>$tno])->first();
            if ($ticket) {
                if (      $ticket->status ==1 ){
                    return ['status'=>false,
                        'message' => '此票已验,检验时间 :'.$ticket->updated_at,
                    ];
                }elseif(      $ticket->status ==2 ){
                    return ['status'=>false,
                        'message' => '此票已退 ,退票时间:'.$ticket->updated_at,
                    ];
                }
                $ticket->status = 1;
                $ticket->check_id = $user->id;
                if ($ticket->save()) {
                    return ['status'=>true,
                        'message' => '正确!!!!!!!!!',
                        'tickets'=> $user->checkedTickets(),
                        'payer'=> $ticket->payers
                        ,];
                }
            }else{
                return ['status'=>false,
                    'message' => '查无此票,请注意',
                ];
            }

        }

    }
}