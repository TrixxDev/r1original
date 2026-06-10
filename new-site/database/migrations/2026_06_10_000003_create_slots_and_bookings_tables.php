<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Слоты (сетка времени + мягкая резервация) и брони (данные клиента).
 * Старая таблица slots хранила данные клиента JSON-текстом в takenby —
 * теперь это отдельная таблица bookings с нормальными колонками.
 *
 * UNIQUE (date, queue_id, position) гарантирует отсутствие дублей слота
 * на уровне СУБД; UNIQUE bookings.slot_id — одну бронь на слот.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('queue_id')->constrained('queues');
            $table->date('date');
            $table->unsignedInteger('position');                 // бывш. iorder
            $table->unsignedTinyInteger('status')->default(0);   // 0 свободен, 1 занят, 2 заблокирован
            // Оптимистичная блокировка + мягкая резервация (5 минут, до 2 продлений)
            $table->unsignedInteger('version')->default(0);
            $table->dateTime('reserved_until')->nullable();
            $table->string('reserved_by', 100)->nullable();
            $table->unsignedTinyInteger('extension_count')->default(0);
            $table->text('comment')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('legacy_id')->nullable()->unique();
            $table->timestamps();

            $table->unique(['date', 'queue_id', 'position']);
            $table->index('reserved_until');
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slot_id')->unique()->constrained('slots')->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->string('cancel_code', 64)->unique();         // бывш. cancel_id
            $table->string('car_brand', 100)->nullable();
            $table->string('car_model', 100)->nullable();
            $table->string('license_plate', 20)->nullable();
            $table->unsignedTinyInteger('rims_with')->nullable(); // 1 без дисков, 2 с дисками
            $table->string('phone_number', 30)->nullable();
            $table->string('email')->nullable();
            $table->text('customer_comment')->nullable();
            $table->string('discount', 50)->nullable();
            $table->boolean('is_mobile')->default(false);
            // Рабочий процесс мастеров (мобильное приложение)
            $table->unsignedTinyInteger('work_status')->default(0); // бывш. mobile_status
            $table->string('ic_status', 20)->nullable();
            $table->string('planned_tasks')->nullable();
            $table->unsignedInteger('lift_spot')->nullable();
            // Снапшот CarInfo API
            $table->json('car_info')->nullable();
            $table->string('car_info_vnr', 16)->nullable();
            $table->dateTime('car_info_fetched_at')->nullable();
            $table->string('car_info_source', 32)->nullable();
            // Полный исходный JSON из старой slots.takenby — страховка от потери
            // данных при переносе (старые записи имели разные форматы полей).
            $table->json('legacy_data')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('license_plate');
            $table->index('phone_number');
            $table->index('car_info_vnr');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('slots');
    }
};
