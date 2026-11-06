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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('source', 20)->index(); // callback | counselling
            $table->string('name');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->string('city')->nullable();
            $table->string('interest', 20)->nullable();
            $table->string('level', 10)->nullable();
            $table->string('country', 10)->nullable();
            $table->string('intake', 10)->nullable();
            $table->boolean('consent')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
