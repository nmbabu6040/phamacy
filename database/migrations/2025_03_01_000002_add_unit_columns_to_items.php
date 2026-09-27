<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `quantity` on purchase_items / sale_items always stays a BASE-unit (piece) count —
 * every existing stock/FEFO/profit calculation keeps working unchanged.
 * These new columns are purely for what the cashier/purchaser actually picked & saw:
 * e.g. quantity=100 (base), but unit_qty=1, product_unit_id -> "Box".
 */
return new class extends Migration {
    public function up(): void {
        Schema::table("purchase_items", function (Blueprint $table) {
            $table->foreignId("product_unit_id")->nullable()->after("product_id")->constrained()->nullOnDelete();
            $table->unsignedInteger("unit_qty")->nullable()->after("product_unit_id");
        });
        Schema::table("sale_items", function (Blueprint $table) {
            $table->foreignId("product_unit_id")->nullable()->after("product_id")->constrained()->nullOnDelete();
            $table->unsignedInteger("unit_qty")->nullable()->after("product_unit_id");
        });
    }
    public function down(): void {
        Schema::table("purchase_items", function (Blueprint $table) { $table->dropConstrainedForeignId("product_unit_id"); $table->dropColumn("unit_qty"); });
        Schema::table("sale_items", function (Blueprint $table) { $table->dropConstrainedForeignId("product_unit_id"); $table->dropColumn("unit_qty"); });
    }
};
