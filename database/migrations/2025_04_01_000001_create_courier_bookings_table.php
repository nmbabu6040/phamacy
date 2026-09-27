<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("courier_bookings", function (Blueprint $table) {
            $table->id();
            $table->foreignId("sale_id")->nullable()->constrained()->nullOnDelete(); // linked online order, if any
            $table->foreignId("branch_id")->nullable()->constrained()->nullOnDelete();
            $table->string("provider"); // steadfast, pathao, redx, ...
            $table->string("invoice_reference"); // our own reference sent to the courier
            $table->string("consignment_id")->nullable();  // ID returned by the courier
            $table->string("tracking_code")->nullable();
            $table->string("recipient_name");
            $table->string("recipient_phone");
            $table->text("recipient_address");
            $table->decimal("cod_amount", 12, 2)->default(0);
            $table->text("item_description")->nullable();
            $table->decimal("weight", 8, 2)->nullable();
            $table->enum("status", ["pending","processing","booked","in_transit","delivered","cancelled","failed"])->default("pending");
            $table->text("failure_reason")->nullable();
            $table->json("response_payload")->nullable(); // raw API response, for debugging
            $table->foreignId("booked_by")->nullable()->constrained("users")->nullOnDelete();
            $table->timestamps();

            $table->index(["provider", "status"]);
        });
    }
    public function down(): void { Schema::dropIfExists("courier_bookings"); }
};
