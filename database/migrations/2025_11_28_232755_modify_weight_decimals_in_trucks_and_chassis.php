<?php

declare(strict_types=1);

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
        // Modificar trucks: tare a 3 decimales
        Schema::table('trucks', function (Blueprint $table) {
            $table->decimal('tare', 10, 3)->nullable()->comment('Tara del vehículo en toneladas')->change();
        });

        // Modificar chassis: tare y safe_weight a 3 decimales
        Schema::table('chassis', function (Blueprint $table) {
            $table->decimal('tare', 8, 3)->nullable()->change();
            $table->decimal('safe_weight', 8, 3)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir trucks: tare a 2 decimales
        Schema::table('trucks', function (Blueprint $table) {
            $table->decimal('tare', 10, 2)->nullable()->comment('Tara del vehículo en toneladas')->change();
        });

        // Revertir chassis: tare y safe_weight a 2 decimales
        Schema::table('chassis', function (Blueprint $table) {
            $table->decimal('tare', 8, 2)->nullable()->change();
            $table->decimal('safe_weight', 8, 2)->nullable()->change();
        });
    }
};
