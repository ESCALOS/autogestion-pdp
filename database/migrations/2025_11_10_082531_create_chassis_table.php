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
        Schema::create('chassis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('restrict');
            $table->string('license_plate', 10);
            $table->string('status')->default(1)->comment('1: Inactivo, 2: Activo, 3: Necesita Actualización');
            $table->string('vehicle_type')->nullable();
            $table->integer('axle_count')->nullable();
            $table->boolean('has_bonus')->default(false);
            $table->decimal('tare', 8, 2)->nullable();
            $table->decimal('safe_weight', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->decimal('length', 8, 2)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->boolean('is_insulated')->default(false);
            $table->string('material')->nullable();
            $table->boolean('accepts_20ft')->default(false);
            $table->boolean('accepts_40ft')->default(false);
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
        Schema::dropIfExists('chassis');
    }
};
