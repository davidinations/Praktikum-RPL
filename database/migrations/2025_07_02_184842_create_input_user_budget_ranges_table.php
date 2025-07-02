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
        Schema::create('input_user_budget_ranges', function (Blueprint $table) {
            $table->id();
            $table->string('id_input');
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_kriteria');
            $table->bigInteger('min_budget');
            $table->bigInteger('max_budget');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('id_kriteria')->references('id_kriteria')->on('master_kriteria')->onDelete('cascade');

            // Unique constraint to prevent duplicate entries
            $table->unique(['id_input', 'id_user', 'id_kriteria']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('input_user_budget_ranges');
    }
};
