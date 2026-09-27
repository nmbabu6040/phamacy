<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Usage in routes: ->middleware("permission:product.view")
     * Multiple allowed: ->middleware("permission:product.view|product.create")
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, "Unauthorized.");
        }

        if ($user->isAdmin()) {
            return $next($request);
        }

        $allowed = collect(explode("|", $permission))
            ->contains(fn ($slug) => $user->can($slug));

        if (!$allowed) {
            abort(403, "You do not have permission to access this page.");
        }

        return $next($request);
    }
}
