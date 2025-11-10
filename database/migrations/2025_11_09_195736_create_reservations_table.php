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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('table_id')->nullable()->constrained('tables')->nullOnDelete();

            // Dados do guest (se user_id for null ou is_guest=true)
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('guest_phone')->nullable();

            // Dados da reserva
            $table->dateTime('reservation_datetime');
            $table->integer('party_size'); // Número de pessoas
            $table->integer('duration'); // Duração em minutos (copiado do restaurant)
            $table->text('notes')->nullable(); // Observações do cliente

            // Status
            $table->enum('status', [
                'pending',
                'confirmed',
                'cancelled_by_customer',
                'cancelled_by_restaurant',
                'completed',
                'no_show',
            ])->default('pending');

            // Cancelamento
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_token', 64)->nullable()->unique(); // Token para cancelamento via link

            // Notificações
            $table->timestamp('reminder_sent_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices para otimização de queries
            $table->index(['restaurant_id', 'status']);
            $table->index(['restaurant_id', 'reservation_datetime']);
            $table->index(['user_id', 'status']);
            $table->index(['table_id', 'reservation_datetime']);
            $table->index('reservation_datetime');
            $table->index('cancel_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
