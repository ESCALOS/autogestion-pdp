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
        Schema::create('company_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('restrict');
            $table->string('type')->comment('1: Ficha RUC, 2: DNI Representante, 3: Ficha SUNARP, 4: Vigencia de Poder');
            $table->string('path');
            $table->integer('status')->default(1)->comment('1: Pendiente, 2: Aprobado, 3: Rechazado');
            $table->text('rejection_reason')->nullable();
            $table->date('submitted_date')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->date('validated_date')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_documents');
    }
};
