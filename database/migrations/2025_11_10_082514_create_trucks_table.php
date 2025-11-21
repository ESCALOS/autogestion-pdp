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
        Schema::create('trucks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('restrict');
            $table->string('license_plate', 10);
            $table->integer('status')->default(1)->comment('1: Inactivo, 2: Activo, 3: Necesita Actualización');
            $table->string('nationality')->nullable()->comment('Nacionalidad del vehículo');
            $table->boolean('is_internal')->default(false)->comment('Si es interno');
            $table->string('truck_type')->nullable()->comment('Tipo: T3, T-Especial, etc.');
            $table->boolean('has_bonus')->default(false)->comment('Si tiene bonificación');
            $table->decimal('tare', 10, 2)->nullable()->comment('Tara del vehículo en toneladas');
            $table->string('appeal_token')->nullable();
            $table->timestamp('appeal_token_expires_at')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'license_plate']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trucks');
    }
};
