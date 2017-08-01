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

class CheckController extends BaseController
{

    public function checkTicket(Request $request)
    {
        $token = $request->token;
        if ($token) {
            /*
             * 1 根据Token 获取 ticket
             *
             * 2 更新 ticket 的status 和 check_id
             *
             *
             */
            $ticket = Ticket::where(['token'=>$token])->first();

            $ticket->status =1 ;
            $ticket->check_id =1 ;
            $ticket->save();

        }

    }
}