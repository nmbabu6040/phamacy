<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("categories", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("slug")->unique();
            $table->string("image")->nullable();
            $table->boolean("status")->default(1);
            $table->timestamps();
        });

        // Generic name of medicine e.g. Paracetamol, Amoxicillin
        Schema::create("generics", function (Blueprint $table) {
            $table->id();
            $table->string("name")->unique();
            $table->string("slug")->unique();
            $table->text("description")->nullable();
            $table->timestamps();
        });

        Schema::create("units", function (Blueprint $table) {
            $table->id();
            $table->string("name");      // Piece, Box, Bottle, Strip
            $table->string("short_name"); // pcs, box
            $table->timestamps();
        });

        Schema::create("brands", function (Blueprint $table) {
            $table->id();
            $table->string("name");   // manufacturer e.g. Square, Beximco
            $table->string("slug")->unique();
            $table->string("logo")->nullable();
            $table->timestamps();
        });

        Schema::create("suppliers", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("company_name")->nullable();
            $table->string("phone")->nullable();
            $table->string("email")->nullable();
            $table->text("address")->nullable();
            $table->decimal("previous_due", 12, 2)->default(0);
            $table->boolean("status")->default(1);
            $table->timestamps();
        });

        Schema::create("customers", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("phone")->nullable();
            $table->string("email")->nullable();
            $table->text("address")->nullable();
            $table->decimal("previous_due", 12, 2)->default(0);
            $table->boolean("status")->default(1);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists("customers");
        Schema::dropIfExists("suppliers");
        Schema::dropIfExists("brands");
        Schema::dropIfExists("units");
        Schema::dropIfExists("generics");
        Schema::dropIfExists("categories");
    }
};
