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
        Schema::table('documents', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('status');
            $table->foreignId('validated_by')->nullable()->constrained('users')->after('rejection_reason');
            $table->timestamp('validated_date')->nullable()->after('validated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
            $table->dropForeign(['validated_by']);
            $table->dropColumn('validated_by');
            $table->dropColumn('validated_date');
        });
    }
};
