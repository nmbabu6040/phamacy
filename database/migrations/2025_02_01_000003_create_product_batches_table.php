<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("product_batches", function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_id")->constrained()->cascadeOnDelete();
            $table->foreignId("branch_id")->constrained()->cascadeOnDelete();
            $table->foreignId("purchase_item_id")->nullable()->constrained()->nullOnDelete();
            $table->string("batch_no")->unique();
            $table->integer("quantity");        // remaining quantity in this batch
            $table->integer("initial_quantity");
            $table->decimal("purchase_price", 12, 2)->default(0);
            $table->decimal("sale_price", 12, 2)->default(0);
            $table->date("manufacturing_date")->nullable();
            $table->date("expiry_date")->nullable();
            $table->timestamps();

            $table->index(["product_id", "branch_id", "expiry_date"]);
        });
    }
    public function down(): void { Schema::dropIfExists("product_batches"); }
};
