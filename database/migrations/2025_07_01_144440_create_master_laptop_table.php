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
        Schema::create('master_laptop', function (Blueprint $table) {
            $table->id('id_laptop');
            $table->unsignedBigInteger('id_admin')->nullable();
            $table->string('merek')->nullable();
            $table->string('model')->nullable();
            $table->integer('harga')->nullable();
            $table->string('processor')->nullable();
            $table->integer('ram')->nullable();
            $table->integer('storage')->nullable();
            $table->string('gpu')->nullable();
            $table->integer('ukuran_baterai')->nullable();
            $table->text('gambar')->nullable();
            $table->timestamps();

            $table->foreign('id_admin')->references('id_admin')->on('admins')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_laptop');
    }
};
