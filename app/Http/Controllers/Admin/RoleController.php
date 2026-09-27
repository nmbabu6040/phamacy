<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount("users")->latest()->get();
        return view("admin.roles.index", compact("roles"));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            "name" => "required|string|max:255",
            "description" => "nullable|string",
        ]);
        $data["slug"] = Str::slug($data["name"]);

        $role = Role::create($data);
        ActivityLog::record("created", "Role", "Created role: {$role->name}", $role);

        return back()->with("success", "Role created successfully.");
    }

    /** Permission matrix: grouped checkboxes by module, saved via sync(). */
    public function permissions(Role $role)
    {
        $permissions = Permission::orderBy("module")->get()->groupBy("module");
        $assigned = $role->permissions()->pluck("permissions.id")->toArray();

        return view("admin.roles.permissions", compact("role", "permissions", "assigned"));
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $request->validate(["permissions" => "array"]);
        $role->permissions()->sync($request->permissions ?? []);

        ActivityLog::record("updated", "Role", "Updated permissions for role: {$role->name}", $role);

        return back()->with("success", "Permissions updated successfully.");
    }

    public function destroy(Role $role)
    {
        if ($role->slug === "admin") {
            return back()->with("error", "The Admin role cannot be deleted.");
        }
        $role->delete();
        ActivityLog::record("deleted", "Role", "Deleted role: {$role->name}");

        return back()->with("success", "Role deleted successfully.");
    }
}
