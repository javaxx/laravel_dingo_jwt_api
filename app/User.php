<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    protected $guarded = [];

    public function checkedTickets()
    {
        return $this->hasMany(\App\Ticket::class, 'check_id', 'id')->with('payers')->latest('updated_at')->whereDate('updated_at',date('y-m-d',time()));
    }


    public function payers()
    {
        return $this->belongsToMany('App\Payer','user_payer')->withTimestamps();
    }
        //用户拥有那些角色
    public function  roles(){
        return $this->belongsToMany(\App\AdminRole::class,'admin_role_user','user_id','role_id');
    }
    //判断是否有某个角色
    public function isInRoles($roles)
    {
        return !!$roles->intersect($this->roles())->count;
    }
    //分配角色
    public function assignRole($role)
    {
        return $this->roles()->save($role);
    }
    //取消角色
    public function delRole($role)
    {
        return $this->roles()->detach($role);
    }
    //用户是否有权限
    public function hasPermission($permission)
    {
        //TODOm
       return $this->isInRoles($permission->roles);
    }

}
