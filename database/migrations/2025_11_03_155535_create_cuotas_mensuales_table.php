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
        Schema::create('cuotas_mensuales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hermano_id')->constrained('familiares')->onDelete('cascade');
            $table->year('anio'); // Año de la cuota
            $table->tinyInteger('mes'); // Mes de la cuota (1-12)
            $table->decimal('monto', 10, 2); // Monto de la cuota
            $table->enum('estado', ['pendiente', 'pagado', 'parcial'])->default('pendiente');
            $table->date('fecha_pago')->nullable(); // Fecha en que se pagó
            $table->string('comprobante', 255)->nullable(); // Ruta del archivo de comprobante
            $table->text('notas')->nullable(); // Notas adicionales
            $table->timestamps();
            
            // Índice único para evitar duplicados de cuota en el mismo mes/año para el mismo hermano
            $table->unique(['hermano_id', 'anio', 'mes'], 'cuota_unica');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuotas_mensuales');
    }
};
