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
        Schema::create('ideas_regalos', function (Blueprint $table) {
            $table->id();
            $table->string('idea', 255);
            $table->decimal('precio_estimado', 8, 2)->nullable();
            $table->string('link_compra', 255)->nullable();
            $table->boolean('comprado')->default(false);
            $table->foreignId('familiar_id')->constrained('familiares')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ideas_regalos');
    }
};

