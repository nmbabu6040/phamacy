<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table("users", function (Blueprint $table) {
            $table->foreignId("branch_id")->nullable()->after("role_id")->constrained()->nullOnDelete();
        });
        Schema::table("purchases", function (Blueprint $table) {
            $table->foreignId("branch_id")->nullable()->after("id")->constrained()->nullOnDelete();
        });
        Schema::table("sales", function (Blueprint $table) {
            $table->foreignId("branch_id")->nullable()->after("id")->constrained()->nullOnDelete();
        });
        Schema::table("expenses", function (Blueprint $table) {
            $table->foreignId("branch_id")->nullable()->after("id")->constrained()->nullOnDelete();
        });
        Schema::table("stock_movements", function (Blueprint $table) {
            $table->foreignId("branch_id")->nullable()->after("product_id")->constrained()->nullOnDelete();
        });
    }
    public function down(): void {
        foreach (["users","purchases","sales","expenses","stock_movements"] as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->dropConstrainedForeignId("branch_id");
            });
        }
    }
};
