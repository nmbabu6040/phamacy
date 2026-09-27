<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("activity_logs", function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->nullable()->constrained()->nullOnDelete();
            $table->string("action");             // created, updated, deleted, login, logout
            $table->string("module")->nullable(); // Product, Sale, Purchase...
            $table->text("description")->nullable();
            $table->string("subject_type")->nullable();
            $table->unsignedBigInteger("subject_id")->nullable();
            $table->json("properties")->nullable(); // old/new values
            $table->string("ip_address")->nullable();
            $table->string("user_agent")->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists("activity_logs"); }
};
