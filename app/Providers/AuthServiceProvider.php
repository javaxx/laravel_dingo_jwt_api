<?php

namespace App\Providers;

use App\AdminPermission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
      /*  $permisssions = AdminPermission::all();
        foreach ($permisssions as $permisssion) {
            Gate::define($permisssion->name, function ($user)use ($permisssion) {
                return $user->hasPermission($permisssion);
            });
        }*/
    }
}
