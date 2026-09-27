<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table("sales", function (Blueprint $table) {
            $table->enum("channel", ["pos", "online"])->default("pos")->after("branch_id");
            $table->enum("order_status", ["pending","processing","shipped","delivered","cancelled","completed"])
                  ->default("completed")->after("status");
            $table->string("customer_name")->nullable()->after("customer_id");
            $table->string("customer_phone")->nullable();
            $table->string("customer_email")->nullable();
            $table->text("shipping_address")->nullable();
            $table->decimal("shipping_cost", 12, 2)->default(0);
        });

        Schema::table("sale_items", function (Blueprint $table) {
            $table->json("batch_allocations")->nullable()->after("subtotal"); // [{batch_id,batch_no,qty}]
        });

        Schema::table("purchase_items", function (Blueprint $table) {
            $table->string("batch_no")->nullable()->after("product_id");
            $table->date("manufacturing_date")->nullable()->after("expiry_date");
        });
    }
    public function down(): void {
        Schema::table("sales", function (Blueprint $table) {
            $table->dropColumn(["channel","order_status","customer_name","customer_phone","customer_email","shipping_address","shipping_cost"]);
        });
        Schema::table("sale_items", function (Blueprint $table) { $table->dropColumn("batch_allocations"); });
        Schema::table("purchase_items", function (Blueprint $table) { $table->dropColumn(["batch_no","manufacturing_date"]); });
    }
};
