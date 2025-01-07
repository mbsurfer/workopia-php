<?php

namespace Framework\Middleware;

use Framework\Session;

class Authorize
{

    /**
     * Check if user is authenticated
     *
     * @return boolean
     */
    public function isAuthenticated()
    {
        return Session::has('user');
    }

    /**
     * Handle the user's request
     *
     * @param string $role
     * @return void
     */
    public function handle($role = '')
    {
        // Redirect authenticated users to homepage if they vistit the route (ie. /login)
        // Routes associated for the guest role are only allowed for users who are not logged in
        if ($role === 'guest' && $this->isAuthenticated()) {
            return redirect('/');
        }
        // Redirct unauthenticated visitors away from the route (ie. /listings/create)
        else if ($role === 'auth' && !$this->isAuthenticated()) {
            return redirect('/auth/login');
        }
    }
}
