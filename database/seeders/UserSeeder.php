<?php
namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where("slug", "admin")->first();
        $managerRole = Role::where("slug", "manager")->first();
        $salesmanRole = Role::where("slug", "salesman")->first();
        $mainBranch = Branch::main();
        $secondBranch = Branch::where("code", "UTR")->first();

        // updateOrCreate on email: will not duplicate if the user already exists locally.
        User::updateOrCreate(
            ["email" => "admin@pharmacy.test"],
            [
                "role_id" => $adminRole?->id,
                "branch_id" => $mainBranch?->id, // admin can still switch branches from the topbar
                "name" => "System Admin",
                "phone" => "01700000000",
                "password" => Hash::make("password"),
                "status" => "active",
                "email_verified_at" => now(),
            ]
        );

        User::updateOrCreate(
            ["email" => "manager@pharmacy.test"],
            [
                "role_id" => $managerRole?->id,
                "branch_id" => $mainBranch?->id,
                "name" => "Store Manager",
                "phone" => "01700000001",
                "password" => Hash::make("password"),
                "status" => "active",
                "email_verified_at" => now(),
            ]
        );

        User::updateOrCreate(
            ["email" => "sales@pharmacy.test"],
            [
                "role_id" => $salesmanRole?->id,
                "branch_id" => $secondBranch?->id ?? $mainBranch?->id,
                "name" => "Sales Counter",
                "phone" => "01700000002",
                "password" => Hash::make("password"),
                "status" => "active",
                "email_verified_at" => now(),
            ]
        );
    }
}
