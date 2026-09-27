<?php
namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ["name" => "Admin",       "slug" => "admin",       "description" => "Full system access"],
            ["name" => "Manager",     "slug" => "manager",      "description" => "Manage inventory, purchase, sales & reports"],
            ["name" => "Pharmacist",  "slug" => "pharmacist",   "description" => "Manage products & stock"],
            ["name" => "Salesman",    "slug" => "salesman",     "description" => "POS sales only"],
            ["name" => "Accountant",  "slug" => "accountant",   "description" => "View reports, manage expenses"],
        ];

        // firstOrCreate = idempotent: existing local rows are left untouched, missing ones are seeded.
        foreach ($roles as $role) {
            Role::firstOrCreate(["slug" => $role["slug"]], $role);
        }

        $modules = ["product","category","purchase","sale","customer","supplier","expense","report","user","role","setting"];
        $actions = ["view","create","edit","delete"];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(
                    ["slug" => "{$module}.{$action}"],
                    ["name" => ucfirst($action) . " " . ucfirst($module), "module" => ucfirst($module)]
                );
            }
        }

        // Give Manager everything except user/role management
        $manager = Role::where("slug", "manager")->first();
        $managerPerms = Permission::whereNotIn("module", ["User", "Role"])->pluck("id");
        $manager?->permissions()->sync($managerPerms);

        // Pharmacist: product + purchase management
        $pharmacist = Role::where("slug", "pharmacist")->first();
        $pharmacistPerms = Permission::whereIn("module", ["Product", "Category", "Purchase", "Supplier"])->pluck("id");
        $pharmacist?->permissions()->sync($pharmacistPerms);

        // Salesman: sale + customer view/create only
        $salesman = Role::where("slug", "salesman")->first();
        $salesmanPerms = Permission::whereIn("module", ["Sale", "Customer"])->whereIn("slug", [
            "sale.view","sale.create","customer.view","customer.create",
        ])->pluck("id");
        $salesman?->permissions()->sync($salesmanPerms);

        // Accountant: reports + expenses
        $accountant = Role::where("slug", "accountant")->first();
        $accountantPerms = Permission::whereIn("module", ["Report", "Expense"])->pluck("id");
        $accountant?->permissions()->sync($accountantPerms);
    }
}
