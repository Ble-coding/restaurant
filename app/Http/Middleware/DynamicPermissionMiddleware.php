<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
// use Illuminate\Support\Facades\Auth;

class DynamicPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next,$resource): Response
    // {
    //     // Récupérez l'action de la méthode HTTP ou des segments d'URL
    //     $action = $this->getAction($request->method());

    //     // Construire la permission (e.g., "delete-blogs")
    //     $permission = "{$action}-{$resource}";

    //     // Vérifiez si l'utilisateur a cette permission
    //     if (!Auth::user()->can($permission)) {
    //         abort(403, 'Vous n\'avez pas la permission d\'accéder à cette ressource.');
    //     }

    //     return $next($request);
    // }

    // private function getAction($method)
    // {
    //     return match ($method) {
    //         'GET' => 'read',
    //         'POST' => 'create',
    //         'PUT', 'PATCH' => 'edit',
    //         'DELETE' => 'delete',
    //         default => null,
    //     };
    // }
}
