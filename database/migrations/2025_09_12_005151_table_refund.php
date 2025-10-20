<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("refund", function (Blueprint $table) {
            $table->id();
            $table->foreignId("booking_id")->nullable();
            $table->string("amount")->nullable();
            $table->string("status")->default("pending");
            $table->string("phone")->nullable();
            $table->string("fullname")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("refund");
    }
};
