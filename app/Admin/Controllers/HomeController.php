<?php
/**
 * Created by PhpStorm.
 * User: si
 * Date: 2017/8/5
 * Time: 15:09
 */

namespace App\Admin\Controllers;


class HomeController extends Controller
{
    public function index()
    {

        return view('admin.home.index');
    }
    public function login()
    {

        return view('admin.home.index');
    }    public function logout()
{

    return view('admin.home.index');
}
}