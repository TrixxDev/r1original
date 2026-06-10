<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Поддержка магазина: корзина (бывш. cart + carts + shop_mobile_cart_items),
 * промокоды, настройки сайта (бывш. cart_config).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('session_id')->nullable();
            $table->json('items');                       // [{product_id, quantity, price}, ...]
            $table->unsignedInteger('total_sum')->default(0); // центы
            $table->timestamps();
            $table->index('user_id');
            $table->index('session_id');
        });

        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('code', 50)->unique();
            $table->date('end_date')->nullable();
            $table->string('type', 10);                  // percentage | fixed (бывш. status 1|2)
            $table->unsignedInteger('value');            // % или центы
            $table->boolean('active')->default(false);
            $table->unsignedInteger('max_uses')->nullable(); // бывш. can_use, NULL = безлимит
            $table->unsignedInteger('used')->default(0);
            $table->timestamps();
        });

        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();             // бывш. cart_config.name (site_season и др.)
            $table->string('value');
            $table->string('label', 100)->nullable();    // бывш. abbr
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('promo_codes');
        Schema::dropIfExists('carts');
    }
};
