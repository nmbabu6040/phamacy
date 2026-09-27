<?php
namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /** Lightweight request logger for admin write actions (POST/PUT/PATCH/DELETE). */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (auth()->check() && in_array($request->method(), ["POST", "PUT", "PATCH", "DELETE"]) && $response->getStatusCode() < 400) {
            $segment = $request->segment(2) ?? "system";
            ActivityLog::record(
                action: strtolower($request->method()),
                module: ucfirst($segment),
                description: auth()->user()->name . " performed " . $request->method() . " on " . $request->path(),
            );
        }

        return $response;
    }
}
