<?php
/**
 * Created by PhpStorm.
 * User: si
 * Date: 2017/8/5
 * Time: 15:09
 */

namespace App\Admin\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class LoginController extends Controller
{
    public function index()
    {
        return view('admin.login.index');
    }
    /*
     * 具体登陆
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:2',
            'password' => 'required|min:2|max:30',
        ]);

        $user = request(['name', 'password']);
        if (true == Auth::guard('web')->attempt($user)) {
            return redirect('/admin/home');
        }

        return Redirect::back()->withErrors("用户名密码错误");
    }
    /*
     * 登出操作
     */
    public function logout()
    {
        \Auth::guard('web')->logout();
        return redirect('/admin/login');
    }
}