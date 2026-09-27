<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with("role")
            ->when($request->search, fn($q) => $q->where("name", "like", "%{$request->search}%")
                ->orWhere("email", "like", "%{$request->search}%"))
            ->latest()->paginate(15)->withQueryString();

        $roles = Role::orderBy("name")->get();

        return view("admin.users.index", compact("users", "roles"));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|email|unique:users,email",
            "phone" => "nullable|string|max:30",
            "role_id" => "required|exists:roles,id",
            "password" => "required|string|min:6",
            "status" => "boolean",
        ]);
        $data["password"] = Hash::make($data["password"]);

        $user = User::create($data);
        ActivityLog::record("created", "User", "Created staff user: {$user->name}", $user);

        return back()->with("success", "User created successfully.");
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|email|unique:users,email,{$user->id}",
            "phone" => "nullable|string|max:30",
            "role_id" => "required|exists:roles,id",
            "status" => "boolean",
            "password" => "nullable|string|min:6",
        ]);

        if (!empty($data["password"])) {
            $data["password"] = Hash::make($data["password"]);
        } else {
            unset($data["password"]);
        }

        $user->update($data);
        ActivityLog::record("updated", "User", "Updated user: {$user->name}", $user);

        return back()->with("success", "User updated successfully.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with("error", "You cannot delete your own account.");
        }
        $user->delete();
        ActivityLog::record("deleted", "User", "Deleted user: {$user->name}");

        return back()->with("success", "User deleted successfully.");
    }
}
