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
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('number'); // "1", "A1", "Varanda 3"
            $table->integer('capacity'); // Número de pessoas
            $table->enum('type', ['internal', 'external', 'window', 'vip', 'bar'])->nullable();
            $table->boolean('is_active')->default(true);

            // Para layout visual (pós-MVP)
            $table->integer('position_x')->nullable();
            $table->integer('position_y')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Uma mesa não pode ter o mesmo número dentro do restaurante
            $table->unique(['restaurant_id', 'number']);

            // Índices para otimização
            $table->index(['restaurant_id', 'is_active']);
            $table->index('capacity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
