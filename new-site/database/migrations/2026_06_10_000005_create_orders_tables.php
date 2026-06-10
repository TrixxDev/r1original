<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Заказы магазина: бывш. orders_ (одна таблица с JSON-позициями)
 * → orders + order_items + payments.
 *
 * Все суммы — int в центах (в старой БД был microсс double/float/int).
 * UNIQUE (provider, transaction_id) в payments даёт идемпотентность
 * повторных callback'ов Paysera на уровне СУБД.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('session_id')->nullable();
            $table->unsignedTinyInteger('status')->default(0);   // бывш. order_status
            // Клиент
            $table->string('customer_name')->nullable();
            $table->string('customer_surname')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_country_code', 5)->nullable();
            $table->string('phone_number', 50)->nullable();
            $table->string('company_reg_nr', 20)->nullable();
            $table->string('company_pvn_nr', 20)->nullable();
            $table->string('company_name')->nullable();
            $table->text('company_address')->nullable();
            $table->text('comments')->nullable();
            $table->text('car_details')->nullable();
            $table->boolean('email_notification')->default(false);
            // Скидка
            $table->string('promo_code', 50)->nullable();
            $table->string('discount_type', 10)->nullable();     // fixed | percentage
            $table->integer('discount_value')->nullable();
            // Суммы в центах
            $table->unsignedInteger('total_price')->default(0);
            $table->unsignedInteger('delivery_price')->nullable();
            $table->unsignedInteger('mounting_price')->nullable();
            // Доставка / монтаж
            $table->unsignedTinyInteger('delivery_method')->nullable(); // 1 монтаж, 2 доставка
            $table->string('delivery_city')->nullable();
            $table->text('delivery_address')->nullable();
            $table->string('door_code', 50)->nullable();
            $table->foreignId('mounting_office_id')->nullable()->constrained('offices')->nullOnDelete();
            $table->text('admin_info')->nullable();
            $table->foreignId('edited_by')->nullable()->constrained('users')->nullOnDelete();
            // Сырой order_details из старой БД — страховка переноса
            $table->json('legacy_details')->nullable();
            $table->timestamp('expires_at')->nullable();         // бывш. delete_at
            $table->unsignedBigInteger('legacy_id')->nullable()->unique();
            $table->timestamps();
            $table->index('status');
            $table->index('expires_at');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('title');                             // снапшот названия
            $table->string('article')->nullable();
            $table->unsignedInteger('unit_price');               // центы, на момент заказа
            $table->unsignedInteger('quantity');
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->unsignedTinyInteger('method'); // 1 при получении, 2 счёт, 3 онлайн
            $table->string('status', 20)->default('pending');
            $table->unsignedInteger('amount');
            $table->string('provider', 20)->nullable();          // 'paysera'
            $table->string('transaction_id')->nullable();
            $table->json('callback_data')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->unique(['provider', 'transaction_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
