<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::withCount(["users","sales","purchases"])->orderByDesc("is_main")->orderBy("name")->get();
        return view("admin.branches.index", compact("branches"));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            "name" => "required|string|max:255",
            "code" => "required|string|max:50|unique:branches,code",
            "phone" => "nullable|string|max:30",
            "email" => "nullable|email",
            "address" => "nullable|string",
            "is_main" => "boolean",
            "status" => "boolean",
        ]);

        if ($request->boolean("is_main")) {
            Branch::where("is_main", true)->update(["is_main" => false]);
        }

        $branch = Branch::create($data);
        ActivityLog::record("created", "Branch", "Created branch: {$branch->name}", $branch);

        return back()->with("success", "Branch created successfully.");
    }

    public function update(Request $request, Branch $branch)
    {
        $data = $request->validate([
            "name" => "required|string|max:255",
            "code" => "required|string|max:50|unique:branches,code,{$branch->id}",
            "phone" => "nullable|string|max:30",
            "email" => "nullable|email",
            "address" => "nullable|string",
            "is_main" => "boolean",
            "status" => "boolean",
        ]);

        if ($request->boolean("is_main")) {
            Branch::where("is_main", true)->where("id", "!=", $branch->id)->update(["is_main" => false]);
        }

        $branch->update($data);
        ActivityLog::record("updated", "Branch", "Updated branch: {$branch->name}", $branch);

        return back()->with("success", "Branch updated successfully.");
    }

    public function destroy(Branch $branch)
    {
        if ($branch->is_main) {
            return back()->with("error", "The main branch cannot be deleted.");
        }
        $branch->delete();
        ActivityLog::record("deleted", "Branch", "Deleted branch: {$branch->name}");

        return back()->with("success", "Branch deleted successfully.");
    }

    /** Lets an admin/head-office user switch which branch their POS/purchase screens operate on. */
    public function switch(Request $request)
    {
        $request->validate(["branch_id" => "required|exists:branches,id"]);
        session(["current_branch_id" => $request->branch_id]);

        return back()->with("success", "Switched active branch.");
    }
}
