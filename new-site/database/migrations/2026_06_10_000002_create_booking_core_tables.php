<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ядро системы записи: филиалы, услуги, очереди, рабочие дни.
 * Соответствие старым таблицам: offices, services, queues,
 * workingdays + new_workingdays (+ legacy rss_workingdays).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->string('shipping', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('office_mobile_prefs', function (Blueprint $table) {
            $table->unsignedBigInteger('office_id')->primary();
            $table->unsignedTinyInteger('lift_slot_count')->default(10);
            $table->timestamp('updated_at')->nullable();
            $table->foreign('office_id')->references('id')->on('offices')->cascadeOnDelete();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('pdf_title')->nullable();
            $table->boolean('allows_storage')->default(false);  // бывш. f_save
            $table->boolean('allows_car')->default(true);       // бывш. f_ac
            $table->boolean('allows_moto')->default(false);     // бывш. f_moto
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained('offices');
            $table->string('title')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_public')->default(true);
            $table->integer('sort_order')->default(0);          // бывш. iorder
            $table->time('time_open')->nullable();
            $table->time('time_close')->nullable();
            $table->time('weekend_time_open')->nullable();      // бывш. wtimeopen
            $table->time('weekend_time_close')->nullable();     // бывш. wtimeclose
            // Шаблоны уведомлений (как в старой queues)
            $table->string('notification_subject')->nullable();
            $table->text('notification_email')->nullable();
            $table->text('notification_cancel_email')->nullable();
            $table->text('notification_sms')->nullable();
            $table->text('notification_schedule_sms')->nullable();
            $table->text('notification_schedule_cancel_sms')->nullable();
            $table->timestamps();
        });

        Schema::create('working_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('queue_id')->constrained('queues')->cascadeOnDelete();
            $table->date('date');
            $table->boolean('is_draft')->default(false);        // заменяет new_workingdays
            $table->time('time_open')->nullable();
            $table->time('time_close')->nullable();
            $table->unsignedTinyInteger('time_step')->default(15);
            $table->boolean('is_opened')->default(true);
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_half')->default(false);         // половинные слоты AC/MOTO
            $table->boolean('ac_toggle')->nullable();
            $table->boolean('moto_toggle')->nullable();
            $table->timestamps();
            $table->unique(['queue_id', 'date', 'is_draft']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('working_days');
        Schema::dropIfExists('queues');
        Schema::dropIfExists('services');
        Schema::dropIfExists('office_mobile_prefs');
        Schema::dropIfExists('offices');
    }
};
