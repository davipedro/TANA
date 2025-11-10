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
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Endereço
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 2)->nullable();
            $table->string('zip_code', 10)->nullable();

            // Contato
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->json('social_media')->nullable(); // {instagram: "@handle", facebook: "url"}

            // Mídias
            $table->string('logo_path')->nullable();
            $table->string('banner_path')->nullable();

            // Configurações de Reserva
            $table->boolean('auto_confirm_reservations')->default(false);
            $table->integer('slot_interval')->default(30); // minutos
            $table->integer('reservation_duration')->default(120); // minutos
            $table->integer('min_advance_hours')->default(2);
            $table->integer('max_advance_days')->default(30);
            $table->integer('min_party_size')->default(1);
            $table->integer('max_party_size')->default(20);

            // Política de Cancelamento
            $table->boolean('cancellation_policy_enabled')->default(true);
            $table->integer('cancellation_hours_before')->default(2);
            $table->text('cancellation_policy_text')->nullable();

            // Sistema
            $table->string('timezone')->default('America/Sao_Paulo');
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('slug');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
