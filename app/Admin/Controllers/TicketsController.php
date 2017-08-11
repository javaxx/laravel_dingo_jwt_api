<?php
/**
 * Created by PhpStorm.
 * User: si
 * Date: 2017/8/9
 * Time: 12:48
 */

namespace App\Admin\Controllers;


use App\Ticket;

class TicketsController extends Controller
{


    public function index(){

       $tickets = Ticket::with(['payers','users'])->where('token','!=','')->paginate(15);

        return view('admin.ticket.index',compact('tickets'));

    }

}