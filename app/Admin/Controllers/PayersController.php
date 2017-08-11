<?php
/**
 * Created by PhpStorm.
 * User: si
 * Date: 2017/8/9
 * Time: 16:20
 */

namespace App\Admin\Controllers;


use App\Payer;

class PayersController extends Controller
{

    public function  index(){

        $payers = Payer::paginate(15);
        return view('admin.payer.index',compact('payers'));
    }
}