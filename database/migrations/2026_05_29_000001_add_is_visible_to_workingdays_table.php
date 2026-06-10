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
        if (Schema::hasTable('workingdays') && ! Schema::hasColumn('workingdays', 'is_visible')) {
            Schema::table('workingdays', function (Blueprint $table) {
                $table->tinyInteger('is_visible')->default(1)->after('is_opened');
            });
        }

        if (Schema::hasTable('new_workingdays') && ! Schema::hasColumn('new_workingdays', 'is_visible')) {
            Schema::table('new_workingdays', function (Blueprint $table) {
                $table->tinyInteger('is_visible')->default(1)->after('is_opened');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('new_workingdays', 'is_visible')) {
            Schema::table('new_workingdays', function (Blueprint $table) {
                $table->dropColumn('is_visible');
            });
        }

        if (Schema::hasColumn('workingdays', 'is_visible')) {
            Schema::table('workingdays', function (Blueprint $table) {
                $table->dropColumn('is_visible');
            });
        }
    }
};
