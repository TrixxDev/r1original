<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('surname')->nullable()->after('name');
            $table->string('username', 50)->nullable()->unique()->after('surname');
            $table->string('phone_number', 20)->nullable()->after('email');
            $table->boolean('enabled')->default(true)->after('remember_token');
            $table->dateTime('last_activity_at')->nullable()->after('enabled');
            // Реквизиты компании (в старой БД отсутствовали, но использовались кодом)
            $table->string('company_name')->nullable();
            $table->string('company_vat', 20)->nullable();
            $table->string('company_address')->nullable();
            $table->string('company_postcode', 10)->nullable();
            $table->string('company_city', 100)->nullable();
            $table->unsignedBigInteger('legacy_id')->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'surname', 'username', 'phone_number', 'enabled', 'last_activity_at',
                'company_name', 'company_vat', 'company_address', 'company_postcode',
                'company_city', 'legacy_id',
            ]);
        });
    }
};
