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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('restrict');
            $table->integer('document_type')->comment('1: DNI, 2: Carné de Extranjería');
            $table->string('document_number', 20);
            $table->string('name');
            $table->string('lastname');
            $table->integer('status')->default(1)->comment('1: Inactivo, 2: Activo, 3: Necesita Actualización, 4: Espera de aprobación, 5: Revisión Documentos, 6: Documentos Infectados');
            $table->string('appeal_token', 64)->nullable()->unique();
            $table->timestamp('appeal_token_expires_at')->nullable();
            $table->string('license_number', 50)->comment('Número de licencia de conducir');
            $table->timestamps();

            $table->unique(['company_id', 'document_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
