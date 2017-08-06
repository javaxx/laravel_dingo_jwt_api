<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminPermission extends Model
{
    //
    protected $guarded = [];
    //权限属于那个角色
    public function roles()
    {
    return    belongsToMany(\App\AdminRole::class, 'admin_permission_role','permission_id','role_id')->withPivot(['permission_id','role_id']);
    }
}
