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
            // Tipo de cuota: mensual (cuota regular) o especial (colecta especial)
            $table->enum('tipo_cuota', ['mensual', 'especial'])->default('mensual')->after('hermano_id');
            
            // Concepto/motivo de la colecta especial
            $table->string('concepto', 200)->nullable()->after('tipo_cuota');
            
            // Meta de recaudación para colectas especiales
            $table->decimal('meta_total', 10, 2)->nullable()->after('concepto');
            
            // Fecha límite para colectas especiales
            $table->date('fecha_limite')->nullable()->after('meta_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuotas_mensuales', function (Blueprint $table) {
            $table->dropColumn(['tipo_cuota', 'concepto', 'meta_total', 'fecha_limite']);
        });
    }
};
