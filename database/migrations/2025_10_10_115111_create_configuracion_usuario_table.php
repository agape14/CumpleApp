<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('configuracion_usuario', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 100)->unique();
            $table->string('valor', 255)->nullable();
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
        
        // Insertar configuraciones por defecto
        DB::table('configuracion_usuario')->insert([
            [
                'clave' => 'tema',
                'valor' => 'light',
                'descripcion' => 'Tema de la aplicación (light, dark, blue, green)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'clave' => 'color_primario',
                'valor' => '#3B82F6',
                'descripcion' => 'Color primario del tema',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'clave' => 'google_calendar_enabled',
                'valor' => 'false',
                'descripcion' => 'Habilitar integración con Google Calendar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'clave' => 'whatsapp_enabled',
                'valor' => 'false',
                'descripcion' => 'Habilitar notificaciones por WhatsApp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'clave' => 'twilio_account_sid',
                'valor' => '',
                'descripcion' => 'Twilio Account SID para WhatsApp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'clave' => 'twilio_auth_token',
                'valor' => '',
                'descripcion' => 'Twilio Auth Token para WhatsApp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'clave' => 'twilio_whatsapp_number',
                'valor' => '',
                'descripcion' => 'Número de WhatsApp de Twilio',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracion_usuario');
    }
};
