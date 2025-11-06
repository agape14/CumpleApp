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
        // Agregar campos de auditoría a familiares
        Schema::table('familiares', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->constrained('familiares')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('familiares')->onDelete('set null');
        });

        // Agregar campos de auditoría a relaciones_familiares
        Schema::table('relaciones_familiares', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->constrained('familiares')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('familiares')->onDelete('set null');
        });

        // Agregar campos de auditoría a regalos_dados
        Schema::table('regalos_dados', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->constrained('familiares')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('familiares')->onDelete('set null');
        });

        // Agregar campos de auditoría a recordatorios
        Schema::table('recordatorios', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->constrained('familiares')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('familiares')->onDelete('set null');
        });

        // Agregar campos de auditoría a ideas_regalos
        Schema::table('ideas_regalos', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->constrained('familiares')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('familiares')->onDelete('set null');
        });

        // Agregar campos de auditoría a cuotas_mensuales
        Schema::table('cuotas_mensuales', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->constrained('familiares')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('familiares')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('familiares', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['created_by', 'updated_by']);
        });

        Schema::table('relaciones_familiares', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['created_by', 'updated_by']);
        });

        Schema::table('regalos_dados', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['created_by', 'updated_by']);
        });

        Schema::table('recordatorios', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['created_by', 'updated_by']);
        });

        Schema::table('ideas_regalos', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['created_by', 'updated_by']);
        });

        Schema::table('cuotas_mensuales', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['created_by', 'updated_by']);
        });
    }
};
