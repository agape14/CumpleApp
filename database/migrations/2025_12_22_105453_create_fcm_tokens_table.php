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
        Schema::create('fcm_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('familiar_id')->nullable();
            $table->string('token')->unique();
            $table->string('device_type')->nullable(); // 'android', 'ios'
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            
            $table->index('familiar_id');
            $table->index('token');
            
            $table->foreign('familiar_id')
                  ->references('id')
                  ->on('familiares')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fcm_tokens');
    }
};
