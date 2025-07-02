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
        Schema::create('master_kriteria', function (Blueprint $table) {
            $table->id('id_kriteria');
            $table->unsignedBigInteger('id_admin')->nullable();
            $table->string('nama')->nullable();
            $table->string('satuan')->nullable();
            $table->string('jenis')->nullable();
            $table->float('bobot')->nullable();
            $table->timestamps();

            $table->foreign('id_admin')->references('id_admin')->on('admins')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_kriteria');
    }
};
