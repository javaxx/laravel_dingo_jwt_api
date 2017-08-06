<?php
/**
 * Created by PhpStorm.
 * User: si
 * Date: 2017/8/5
 * Time: 15:04
 */

namespace App\Admin\Controllers;


use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller  as BaseController;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

}