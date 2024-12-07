<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{

        /**
     * Gère la requête entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission
     * @return \Symfony\Component\HttpFoundation\Response
     */
    // public function handle(Request $request, Closure $next, $role)
    // {
    //     if (!Auth::check() || !Auth::user()->hasRole($role)) {
    //         abort(403, 'Accès interdit.');
    //     }

    //     return $next($request);
    // }

    public function handle($request, Closure $next, $permission)
    {
        if (!Auth::user()->can($permission)) {
            abort(403, 'Vous n\'avez pas la permission d\'accéder à cette ressource.');
        }

        return $next($request);
    }
}
