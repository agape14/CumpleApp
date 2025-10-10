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
        Schema::create('regalos_dados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('familiar_id')->constrained('familiares')->onDelete('cascade');
            $table->string('nombre_regalo', 200);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2)->nullable();
            $table->date('fecha_entrega');
            $table->enum('ocasion', ['cumpleaños', 'navidad', 'aniversario', 'graduacion', 'otro'])->default('cumpleaños');
            $table->string('lugar_compra', 200)->nullable();
            $table->text('notas')->nullable();
            $table->string('foto', 255)->nullable(); // Ruta de la foto del regalo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regalos_dados');
    }
};
