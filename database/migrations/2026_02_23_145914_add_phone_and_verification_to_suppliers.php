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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('phone_prefix')->nullable()->comment('País y prefijo ej: +51');
            $table->string('phone_number')->nullable()->comment('Número de teléfono sin prefijo');
            $table->string('phone_country')->nullable()->comment('Nombre del país');
            $table->string('verification_code')->nullable()->comment('Código OTP para verificación');
            $table->timestamp('verification_code_expires_at')->nullable()->comment('Expiración del código OTP');
            $table->timestamp('phone_verified_at')->nullable()->comment('Fecha de verificación del teléfono');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['phone_prefix', 'phone_number', 'phone_country', 'verification_code', 'verification_code_expires_at', 'phone_verified_at']);
        });
    }
};
