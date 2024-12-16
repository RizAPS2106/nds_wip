<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RoleMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if ($user->roles->where("nama_role", "admin")->count() > 0) {
            return $next($request);
        }

        foreach($roles as $role) {
            // Check if user has the role This check will depend on how your roles are set up
            foreach ($user->roles as $userRole) {
                if ($userRole->accesses->whereIn("access", [$role, "admin", "superadmin"])->count() > 0) {
                    return $next($request);
                }
            }
        }

        return redirect('home')->with('error', 'You have not access to this module');
    }
}