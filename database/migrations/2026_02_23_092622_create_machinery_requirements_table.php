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
        Schema::create('machinery_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->string('cost_center')->comment('Centro de costo (searchable)');
            $table->string('vessel_name')->comment('Nombre de la nave (searchable)');
            $table->integer('operation_type')->comment('Tipo de operación (enum)');
            $table->integer('unit_type')->comment('Tipo de unidades (enum)');
            $table->integer('units_quantity')->comment('Cantidad de unidades');
            $table->dateTime('activation_time')->comment('Horario de activación');
            $table->integer('days_quantity')->comment('Cantidad de jornadas');
            $table->dateTime('announcement_launched_at')->nullable()->comment('Fecha y hora en que se lanzó la convocatoria');
            $table->timestamps();

            $table->index('cost_center');
            $table->index('vessel_name');
            $table->index('user_id');
            $table->index('activation_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machinery_requirements');
    }
};
