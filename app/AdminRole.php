<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminRole extends Model
{
    //
    protected $guarded = [];

    public function permissions()
    {
        return $this->belongsToMany(\App\AdminPermission::class, 'admin_permission_role','role_id','permission_id');
    }

    public function grantPermission($permission)
    {
        return $this->permissions()->save($permission);
    }

    public function delPermission($permission)
    {
        return $this->permissions()->detach($permission);
    }
    //判断角色是否有权限
    public function hasPermissions($permission)
    {
        return $this->permissions()->contains($permission);
    }
}
