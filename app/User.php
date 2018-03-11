<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    protected $guarded = [];

    public function checkedTickets($date = '' )
    {
        if ($date ==''){
            $date  =   date('y-m-d', time());
        }
        return $this->hasMany(\App\Ticket::class, 'check_id', 'id')->with('payers')->latest('updated_at')->whereDate('updated_at',$date)->get();
    }

    public function tickets()
    {
        return $this->hasMany(\App\Ticket::class, 'user_id', 'id')->with('payers')->latest('created_at');

    }

    //获取优惠券
    public function getCoupon()
    {
        return $this->belongsToMany(\App\Coupon::class,'user_coupons','user_id','coupons_id')->withTimestamps();
    }
    // 获取活动跟随者
    public function getFollower()
    {
        return $this->belongsToMany(\App\User::class,'sharelist','share_uid','follow_uid')->oldest('created_at');

    }
    // 获取活动跟随者
    public function getFollowerByStatus_0()
    {
        return $this->belongsToMany(\App\User::class,'sharelist','share_uid','follow_uid')->oldest('created_at')->where(['status'=>0]);
    }
    //获取活动领导者

    public function getLeader()
    {
        return $this->belongsToMany(\App\User::class,'sharelist','follow_uid','share_uid');

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
