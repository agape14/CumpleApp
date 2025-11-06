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
        Schema::table('cuotas_mensuales', function (Blueprint $table) {
            // Hacer nullable los campos anio y mes para colectas especiales
            $table->integer('anio')->nullable()->change();
            $table->integer('mes')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuotas_mensuales', function (Blueprint $table) {
            // Revertir a NOT NULL (solo si no hay registros con NULL)
            $table->integer('anio')->nullable(false)->change();
            $table->integer('mes')->nullable(false)->change();
        });
    }
};
