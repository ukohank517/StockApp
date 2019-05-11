<?php

namespace DDApp\Providers;

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
        'DDApp\Model' => 'DDApp\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
	
	// user: 10人ほど想定
	// role = 0 -- unknown
	// role = 1 ----------
	// role = 2  管	  ↑
	// role = 3  理   一
	// role = 4  者	  般
	// role = 5 ---	  ユ
	// role = 6 	  |
	// role = 7	  ザ
	// role = 8	  |
	// role = 9	  ↓
	// role = 10----------

        // 知らない人
	Gate::define('unknown-people', function($user){
	    return ($user->role == 0);
	});
	// 管理者以上に許可
	Gate::define('admin-higher', function($user){
	    return ($user->role >= 1 && $user->role <=5);
	});
	// 全ユーザーに許可
	Gate::define('user-higher', function($user){
	    return ($user->role >=1 && $user->role <=10);
	});
    }
}
