<?php
/**
 * Created by PhpStorm.
 * User: si
 * Date: 2017/8/5
 * Time: 15:09
 */

namespace App\Admin\Controllers;


use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class PermissionController extends Controller
{
    /*
     * 用户列表
     */
    public function index()
    {
        $permissions = \App\AdminPermission::paginate(10);
        return view('/admin/permission/index', compact('permissions'));
    }

    //创建页面
    public function create()
    {
        return view('admin.permission.create');


    }
    public function store()
    {
        $this->validate(request(), [
            'name' => 'required|min:3',
            'description' => 'required'
        ]);

        \App\AdminPermission::create(request(['name', 'description']));
        return redirect('/admin/permissions');
    }
}