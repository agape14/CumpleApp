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
        Schema::create('recordatorios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('familiar_id')->constrained('familiares')->onDelete('cascade');
            $table->integer('dias_antes')->default(7); // Días antes del cumpleaños para recordar
            $table->boolean('enviar_email')->default(true);
            $table->boolean('enviar_whatsapp')->default(false);
            $table->boolean('activo')->default(true);
            $table->time('hora_envio')->default('09:00:00'); // Hora del día para enviar el recordatorio
            $table->text('mensaje_personalizado')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recordatorios');
    }
};
