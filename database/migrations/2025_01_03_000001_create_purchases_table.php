<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("purchases", function (Blueprint $table) {
            $table->id();
            $table->string("invoice_no")->unique();
            $table->foreignId("supplier_id")->nullable()->constrained()->nullOnDelete();
            $table->date("purchase_date");
            $table->decimal("total_amount", 12, 2)->default(0);
            $table->decimal("discount", 12, 2)->default(0);
            $table->decimal("tax", 12, 2)->default(0);
            $table->decimal("shipping_cost", 12, 2)->default(0);
            $table->decimal("grand_total", 12, 2)->default(0);
            $table->decimal("paid_amount", 12, 2)->default(0);
            $table->decimal("due_amount", 12, 2)->default(0);
            $table->enum("payment_status", ["paid","partial","due"])->default("due");
            $table->enum("status", ["pending","completed","cancelled"])->default("completed");
            $table->foreignId("created_by")->nullable()->constrained("users")->nullOnDelete();
            $table->text("note")->nullable();
            $table->timestamps();
        });

        Schema::create("purchase_items", function (Blueprint $table) {
            $table->id();
            $table->foreignId("purchase_id")->constrained()->cascadeOnDelete();
            $table->foreignId("product_id")->constrained()->cascadeOnDelete();
            $table->integer("quantity");
            $table->decimal("purchase_price", 12, 2);
            $table->decimal("sale_price", 12, 2)->nullable();
            $table->date("expiry_date")->nullable();
            $table->decimal("subtotal", 12, 2);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists("purchase_items");
        Schema::dropIfExists("purchases");
    }
};
