<?php

namespace App\Http\Middleware;

use App\Exceptions\NotAdminException;
use App\Traits\ApiResponser;
use App\User;
use Closure;

class AdminCheck
{   
    use ApiResponser;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $id = $request->route()->parameters()["id"];
        $user = User::findOrFail($id);

        if($user->isVerified() && $user->isAdmin()) {

            return $next($request);
        }

       throw new NotAdminException("not admin exception");
        
    }
}
