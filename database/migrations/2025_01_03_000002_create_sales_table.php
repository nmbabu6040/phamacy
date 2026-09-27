<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("sales", function (Blueprint $table) {
            $table->id();
            $table->string("invoice_no")->unique();
            $table->foreignId("customer_id")->nullable()->constrained()->nullOnDelete();
            $table->date("sale_date");
            $table->decimal("total_amount", 12, 2)->default(0);
            $table->decimal("discount", 12, 2)->default(0);
            $table->decimal("tax", 12, 2)->default(0);
            $table->decimal("grand_total", 12, 2)->default(0);
            $table->decimal("paid_amount", 12, 2)->default(0);
            $table->decimal("due_amount", 12, 2)->default(0);
            $table->decimal("profit", 12, 2)->default(0);
            $table->enum("payment_method", ["cash","card","mobile_banking","due"])->default("cash");
            $table->enum("payment_status", ["paid","partial","due"])->default("paid");
            $table->enum("status", ["completed","returned","cancelled"])->default("completed");
            $table->foreignId("created_by")->nullable()->constrained("users")->nullOnDelete();
            $table->text("note")->nullable();
            $table->timestamps();
        });

        Schema::create("sale_items", function (Blueprint $table) {
            $table->id();
            $table->foreignId("sale_id")->constrained()->cascadeOnDelete();
            $table->foreignId("product_id")->constrained()->cascadeOnDelete();
            $table->integer("quantity");
            $table->decimal("sale_price", 12, 2);
            $table->decimal("purchase_price", 12, 2)->default(0); // snapshot for profit calc
            $table->decimal("discount", 12, 2)->default(0);
            $table->decimal("subtotal", 12, 2);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists("sale_items");
        Schema::dropIfExists("sales");
    }
};
