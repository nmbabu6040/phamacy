<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("expense_categories", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->timestamps();
        });

        Schema::create("expenses", function (Blueprint $table) {
            $table->id();
            $table->foreignId("expense_category_id")->constrained()->cascadeOnDelete();
            $table->string("title");
            $table->decimal("amount", 12, 2);
            $table->date("expense_date");
            $table->string("attachment")->nullable();
            $table->foreignId("created_by")->nullable()->constrained("users")->nullOnDelete();
            $table->text("note")->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists("expenses");
        Schema::dropIfExists("expense_categories");
    }
};
