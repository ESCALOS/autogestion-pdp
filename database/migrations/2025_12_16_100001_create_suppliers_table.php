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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->integer('type')->default(1)->comment('1: Natural, 2: Jurídica');
            $table->string('ruc', 11)->unique();
            $table->string('business_name');
            $table->integer('status')->default(1)->comment('1: Pendiente, 2: Aprobado, 3: Rechazado');
            $table->boolean('is_active')->default(true);
            $table->string('appeal_token')->nullable()->unique()->after('status');
            $table->timestamp('appeal_token_expires_at')->nullable()->after('appeal_token');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
