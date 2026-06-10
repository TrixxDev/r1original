<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Служебные таблицы: аудит (с индексами, которых не было в старой БД),
 * времена синхронизаций, баннеры, расшифровки кодов шин, подбор по авто.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->dateTime('audit_time')->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('severity', 20)->index();
            $table->string('facility', 50)->nullable();
            $table->string('event')->nullable();
            $table->string('item')->nullable();
            $table->string('subitem')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('url')->nullable();
            $table->string('classname')->nullable();
            $table->json('instance')->nullable();        // снимок объекта
            $table->json('backtrace')->nullable();
        });

        Schema::create('sync_times', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('url')->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('tire_codes', function (Blueprint $table) { // бывш. code
            $table->id();
            $table->string('name', 50);
            $table->text('explanation');
            $table->timestamps();
        });

        // Подбор дисков по автомобилю (бывш. filter_cars/filter_models/filter_sizes)
        Schema::create('car_makes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
        });

        Schema::create('car_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_make_id')->constrained('car_makes')->cascadeOnDelete();
            $table->string('title');
        });

        Schema::create('car_wheel_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_model_id')->constrained('car_models')->cascadeOnDelete();
            $table->string('r', 7)->nullable();
            $table->string('j', 7)->nullable();
            $table->string('j_min', 10)->nullable();
            $table->string('et', 10)->nullable();
            $table->string('skr', 7)->nullable();
            $table->string('pcd', 7)->nullable();
            $table->string('d', 7)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_wheel_sizes');
        Schema::dropIfExists('car_models');
        Schema::dropIfExists('car_makes');
        Schema::dropIfExists('tire_codes');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('sync_times');
        Schema::dropIfExists('audits');
    }
};
