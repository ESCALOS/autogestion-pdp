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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('documentable_type')->comment('Driver, Truck, Chassis');
            $table->integer('documentable_id');
            $table->string('type');
            $table->string('path');
            $table->date('submitted_date')->comment('Fecha de subida del documento');
            $table->date('course_date')->nullable()->after('submitted_date')->comment('Fecha en que se realizó el curso (solo para cursos)');
            $table->date('expiration_date')->comment('Fecha de vencimiento del documento');
            $table->integer('status')->default(1)->comment('pending, approved, rejected');

            $table->index(['documentable_type', 'documentable_id']);
            $table->unique(['documentable_type', 'documentable_id', 'type']);
            $table->index('expiration_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
