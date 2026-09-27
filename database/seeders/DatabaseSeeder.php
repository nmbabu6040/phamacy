<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Master seeder. All child seeders use firstOrCreate()/updateOrCreate(),
     * so this is safe to re-run any time:
     *  - if a record already exists locally -> it is left as-is (not duplicated/overwritten)
     *  - if it does not exist yet -> it gets created from the seeder defaults
     * i.e. "jekhane local data thakbe seta thakbe, jekhane thakbe na sekhane seeder theke asbe".
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            BranchSeeder::class,
            UserSeeder::class,
            CatalogSeeder::class,
            ProductSeeder::class,
            SettingSeeder::class,
            SliderSeeder::class,
            DemoTransactionSeeder::class,
        ]);
    }
}
