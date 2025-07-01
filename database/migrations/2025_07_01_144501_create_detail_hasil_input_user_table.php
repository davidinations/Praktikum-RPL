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
        Schema::create('detail_hasil_input_user', function (Blueprint $table) {
            $table->id('id_detail');
            $table->unsignedBigInteger('id_hasil_input')->nullable();
            $table->unsignedBigInteger('id_kriteria')->nullable();
            $table->float('hasil_kalkulasi')->nullable();
            $table->timestamps();

            $table->foreign('id_hasil_input')->references('id_hasil_input')->on('hasil_input_user')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('id_kriteria')->references('id_kriteria')->on('master_kriteria')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_hasil_input_user');
    }
};
