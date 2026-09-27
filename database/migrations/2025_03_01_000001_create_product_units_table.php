<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("product_units", function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_id")->constrained()->cascadeOnDelete();
            $table->foreignId("unit_id")->constrained()->cascadeOnDelete(); // Piece, Strip, Box, Bottle...
            $table->unsignedInteger("conversion_factor")->default(1); // how many base (piece) units = 1 of this unit
            $table->decimal("purchase_price", 12, 2)->default(0);
            $table->decimal("sale_price", 12, 2)->default(0);
            $table->boolean("is_base_unit")->default(false); // exactly one per product, conversion_factor = 1
            $table->string("barcode")->nullable()->unique(); // each packaging level can carry its own barcode
            $table->timestamps();

            $table->unique(["product_id", "unit_id"]);
        });
    }
    public function down(): void { Schema::dropIfExists("product_units"); }
};
