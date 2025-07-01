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
        Schema::create('hasil_input_user', function (Blueprint $table) {
            $table->id('id_hasil_input');
            $table->unsignedBigInteger('id_input')->nullable();
            $table->unsignedBigInteger('id_laptop')->nullable();
            $table->float('rating')->nullable();
            $table->integer('ranking')->nullable();
            $table->timestamps();

            $table->foreign('id_laptop')->references('id_laptop')->on('master_laptop')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_input_user');
    }
};
