<?php
namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        Branch::firstOrCreate(
            ["code" => "MAIN"],
            [
                "name" => "Main Branch — Dhanmondi",
                "phone" => "+880 1700-000000",
                "email" => "dhanmondi@citypharmacy.test",
                "address" => "House 12, Road 5, Dhanmondi, Dhaka",
                "is_main" => true,
                "status" => 1,
            ]
        );

        Branch::firstOrCreate(
            ["code" => "UTR"],
            [
                "name" => "Uttara Branch",
                "phone" => "+880 1700-000001",
                "email" => "uttara@citypharmacy.test",
                "address" => "Sector 7, Uttara, Dhaka",
                "is_main" => false,
                "status" => 1,
            ]
        );
    }
}
