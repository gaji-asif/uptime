<?php

namespace App\Providers;

use App\Employee;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;

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

        Auth::viaRequest('external-token', function ($request) {
            $userId = $request->input('user_id');
            $token = $request->input('token');

            $user = Employee::find($userId);

            if (!$user) {
                return false;
            }

            $salt = $user->email . $user->created_at;

            if (!Hash::check($salt, $token) ) {
                return false;
            }

            auth('admin')->setUser($user);
            session()->put('employee', $user);

            return $user;
        });

        //  Passport::tokensCan([
        //     'admin'         => 'Admin',
        //     'rep'     => 'Rep',

        // ]);
        // Passport::routes();

        // Passport::tokensExpireIn(now()->addDays(15));
    }
}
