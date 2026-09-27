<?php
namespace App\Http\Middleware;

use App\Models\Branch;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the active branch for the logged-in staff member and shares it as
 * current_branch on every admin view. Order of preference:
 *   1. A branch explicitly switched to in this session (head-office/admin users only)
 *   2. The branch assigned to the user
 *   3. The main branch
 */
class SetCurrentBranch
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            $branchId = session("current_branch_id") ?: $user->branch_id;
            $branch = $branchId ? Branch::find($branchId) : Branch::main();
            $branch ??= Branch::main();

            app()->instance("current_branch", $branch);
            view()->share("currentBranch", $branch);
            view()->share("allBranches", $user->isAdmin() ? Branch::where("status", 1)->orderBy("name")->get() : collect());
        }

        return $next($request);
    }
}
