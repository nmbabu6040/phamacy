<?php
namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\ExpenseCategory;
use App\Models\Generic;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ["Pain Relief","Antibiotics","Vitamins & Supplements","Cold & Flu","Diabetes Care","Skin Care","Baby Care","Cardiac Care"];
        foreach ($categories as $name) {
            Category::firstOrCreate(["slug" => Str::slug($name)], ["name" => $name, "status" => 1]);
        }

        $generics = ["Paracetamol","Amoxicillin","Ibuprofen","Omeprazole","Cetirizine","Metformin","Azithromycin","Losartan","Atorvastatin","Vitamin C"];
        foreach ($generics as $name) {
            Generic::firstOrCreate(["slug" => Str::slug($name)], ["name" => $name]);
        }

        $units = [["Piece","pcs"],["Box","box"],["Bottle","btl"],["Strip","strip"],["Tube","tube"]];
        foreach ($units as [$name, $short]) {
            Unit::firstOrCreate(["name" => $name], ["short_name" => $short]);
        }

        $brands = ["Square Pharma","Beximco","Incepta","Renata","ACI","Opsonin"];
        foreach ($brands as $name) {
            Brand::firstOrCreate(["slug" => Str::slug($name)], ["name" => $name]);
        }

        $suppliers = [
            ["name" => "MediSource Distributors", "company_name" => "MediSource Ltd", "phone" => "01711111111", "email" => "info@medisource.test"],
            ["name" => "HealthPlus Wholesale",     "company_name" => "HealthPlus BD", "phone" => "01722222222", "email" => "sales@healthplus.test"],
        ];
        foreach ($suppliers as $s) {
            Supplier::firstOrCreate(["email" => $s["email"]], $s + ["status" => 1]);
        }

        $customers = [
            ["name" => "Walk-in Customer", "phone" => "N/A"],
            ["name" => "Rahim Uddin", "phone" => "01800000001"],
            ["name" => "Karim Hossain", "phone" => "01800000002"],
        ];
        foreach ($customers as $c) {
            Customer::firstOrCreate(["name" => $c["name"]], $c + ["status" => 1]);
        }

        $expenseCategories = ["Rent","Utility Bill","Staff Salary","Transport","Marketing","Maintenance","Others"];
        foreach ($expenseCategories as $name) {
            ExpenseCategory::firstOrCreate(["name" => $name]);
        }
    }
}
