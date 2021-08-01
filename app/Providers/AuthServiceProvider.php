<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['auth']->viaRequest('api', function ($request) {
            if ($token = $request->bearerToken())
                return User::validateJWT($token);
        });

        Gate::define('edit-ticket', function ($user, $ticket) {
            $is_owner = $ticket->user_id == $user->id;

            return $user->is_admin || $is_owner;
        });

        Gate::define('ticket-product', function ($user, $ticket) {
            $is_pending = $ticket->status == 'pending';

            return $is_pending && Gate::allows('edit-ticket', $ticket);
        });
    }
}
