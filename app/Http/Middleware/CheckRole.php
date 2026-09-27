<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $user = $request->user();

        if (!$user || !$user->role) {
            abort(403, "Unauthorized.");
        }

        $allowedRoles = explode("|", $roles);

        if (!in_array($user->role->slug, $allowedRoles)) {
            abort(403, "You do not have access to this section.");
        }

        return $next($request);
    }
}
