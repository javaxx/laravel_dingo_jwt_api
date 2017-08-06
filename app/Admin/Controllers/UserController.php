<?php
/**
 * Created by PhpStorm.
 * User: si
 * Date: 2017/8/5
 * Time: 15:09
 */

namespace App\Admin\Controllers;


use App\AdminRole;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    //列表
    public function index()
    {

        $users = User::paginate(10);


        return view('admin.user.index',compact('users'));

    }
    //创建页面
    public function create()
    {
        return view('admin.user.create');


    }
    //创建操作
    public function store()
    {

    }

    //
    public function role(User $user)
    {
        $roles = AdminRole::all();
        $myRole = $user->roles;

        return view('admin.user.role',compact('roles','myRole','user'));
    }
//储存用户角色
    public function storeRole(User $user)
    {
        $this->validate(\request(),[
            'roles'=>'array'
        ]);
        $roles = AdminRole::findMany(\request('roles'));
        $myRoles = $user->roles;
        $addRoles = $roles->diff($myRoles);
        foreach ($addRoles as $role) {
            $user->assignRole($role);
        }
        $delRoles = $myRoles->diff($roles);
        foreach ($delRoles as $role) {
            $user->delRole($role);
        }
        return back();
    }
}