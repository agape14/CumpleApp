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
        Schema::create('relaciones_familiares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('familiar_id')->constrained('familiares')->onDelete('cascade');
            $table->foreignId('familiar_relacionado_id')->constrained('familiares')->onDelete('cascade');
            $table->enum('tipo_relacion', ['padre', 'madre', 'hijo', 'hija', 'esposo', 'esposa', 'pareja', 'hermano', 'hermana', 'abuelo', 'abuela', 'nieto', 'nieta', 'tio', 'tia', 'sobrino', 'sobrina', 'primo', 'prima', 'otro']);
            $table->string('descripcion', 200)->nullable();
            $table->timestamps();
            
            // Evitar duplicados de la misma relación
            $table->unique(['familiar_id', 'familiar_relacionado_id', 'tipo_relacion'], 'relacion_unica');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relaciones_familiares');
    }
};
