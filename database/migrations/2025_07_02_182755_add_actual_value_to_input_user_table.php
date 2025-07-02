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
        Schema::table('input_user', function (Blueprint $table) {
            $table->decimal('actual_value', 15, 2)->nullable()->after('value')
                ->comment('Stores actual budget amount for price criteria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('input_user', function (Blueprint $table) {
            $table->dropColumn('actual_value');
        });
    }
};
