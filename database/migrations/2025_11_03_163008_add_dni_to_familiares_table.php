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
        Schema::table('familiares', function (Blueprint $table) {
            $table->string('dni', 20)->unique()->nullable()->after('nombre');
            $table->boolean('puede_acceder')->default(false)->after('dni');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('familiares', function (Blueprint $table) {
            $table->dropColumn(['dni', 'puede_acceder']);
        });
    }
};
