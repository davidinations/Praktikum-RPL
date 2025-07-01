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
        Schema::create('input_user', function (Blueprint $table) {
            $table->id('id_data');
            $table->unsignedBigInteger('id_input')->nullable();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->unsignedBigInteger('id_kriteria')->nullable();
            $table->integer('value')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('id_kriteria')->references('id_kriteria')->on('master_kriteria')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('input_user');
    }
};
