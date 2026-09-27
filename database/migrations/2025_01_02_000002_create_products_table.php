<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("products", function (Blueprint $table) {
            $table->id();
            $table->string("name");                 // Brand/trade name e.g. Napa 500mg
            $table->string("slug")->unique();
            $table->string("code")->unique();        // SKU / barcode
            $table->foreignId("category_id")->nullable()->constrained()->nullOnDelete();
            $table->foreignId("generic_id")->nullable()->constrained()->nullOnDelete();
            $table->foreignId("brand_id")->nullable()->constrained()->nullOnDelete();
            $table->foreignId("unit_id")->nullable()->constrained()->nullOnDelete();
            $table->string("strength")->nullable();   // e.g. 500mg
            $table->enum("dosage_form", ["Tablet","Capsule","Syrup","Injection","Cream","Drops","Inhaler","Other"])->default("Tablet");
            $table->decimal("purchase_price", 12, 2)->default(0);
            $table->decimal("sale_price", 12, 2)->default(0);
            $table->decimal("discount", 12, 2)->default(0);
            $table->decimal("tax_percent", 5, 2)->default(0);
            $table->integer("stock_qty")->default(0);
            $table->integer("alert_qty")->default(10); // low stock threshold
            $table->date("expiry_date")->nullable();
            $table->string("image")->nullable();
            $table->text("description")->nullable();
            $table->boolean("status")->default(1);
            $table->boolean("is_featured")->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists("products"); }
};
