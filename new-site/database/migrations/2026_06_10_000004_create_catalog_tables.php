<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Единый каталог товаров вместо 7 семейств таблиц старой БД
 * (auto/moto/quadr/bigtire/rim/quadrim/studs × brands/treads/tires/stock).
 *
 * Категории: auto | moto | quadr | big | rim | quadrim | stud.
 * Специфика категории — в JSON-колонке attrs (eco/wet/noise, pcd/et/skr,
 * stud_length и т.п.); при необходимости частого поиска по полю attrs
 * добавляется генерируемая индексируемая колонка.
 *
 * legacy_source ('auto:123') обеспечивает идемпотентность переноса.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('category', 10);
            $table->string('title');
            $table->string('slug', 100);
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('legacy_source', 30)->nullable()->unique();
            $table->timestamps();
            $table->unique(['category', 'slug']);
        });

        Schema::create('product_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
            $table->string('category', 10);
            $table->string('title');
            $table->string('slug', 100);
            $table->unsignedTinyInteger('season')->nullable();       // 1 лето, 2 зима
            $table->unsignedTinyInteger('vehicle_type')->nullable(); // 1 легковые, 2 бусы, 3 4x4
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('legacy_source', 30)->nullable()->unique();
            $table->timestamps();
            $table->unique(['category', 'slug']);
            $table->index(['category', 'season']);
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('model_id')->constrained('product_models')->cascadeOnDelete();
            $table->string('category', 10);
            $table->string('article')->nullable();
            // Размер (универсально для шин и дисков)
            $table->string('width', 30)->nullable();       // бывш. d1
            $table->string('profile', 30)->nullable();     // бывш. d2
            $table->string('diameter', 30)->nullable();    // бывш. d3
            $table->string('extra_dim', 30)->nullable();   // бывш. d4 (мото)
            $table->string('load_index', 11)->nullable();  // li
            $table->string('speed_index', 10)->nullable(); // si
            // Цены в центах
            $table->unsignedInteger('price_retail')->nullable();   // бывш. price1
            $table->unsignedInteger('price_partner')->nullable();  // бывш. price2
            $table->unsignedInteger('price_extra')->nullable();    // бывш. price3
            $table->boolean('is_offer')->default(false);
            $table->boolean('is_price_offer')->default(false);
            $table->string('offer_price', 30)->nullable();
            $table->string('offer_text', 30)->nullable();
            $table->boolean('is_top')->default(false);
            $table->boolean('is_used')->default(false);
            $table->unsignedTinyInteger('visible_users')->default(1);
            $table->unsignedTinyInteger('visible_list')->default(1);
            $table->boolean('available')->default(true);
            $table->text('comment')->nullable();
            $table->text('admin_comment')->nullable();     // бывш. acomment
            $table->string('code')->nullable();
            $table->json('attrs')->nullable();
            $table->integer('ordered')->default(0);
            $table->integer('reserved')->default(0);
            $table->string('legacy_source', 30)->nullable()->unique();
            $table->timestamps();
            $table->index('article');
            $table->index(['category', 'width', 'profile', 'diameter'], 'idx_products_size');
        });

        // Свои склады по филиалам: бывш. quantity / urs_quantity / krs_quantity.
        // office_id NULL = центральный (бывш. колонка quantity).
        Schema::create('product_warehouse_stock', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedBigInteger('office_id')->default(0);
            $table->integer('quantity')->default(0);
            $table->primary(['product_id', 'office_id']);
        });

        // Остатки поставщиков: бывш. auto/moto/quadr/bigtire/rim_stock.
        Schema::create('supplier_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('supplier', 10); // i3|duell|starco|gy|rz|rg|accrual
            $table->string('article', 100)->nullable();
            $table->integer('quantity')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['product_id', 'quantity']);
            $table->index(['article', 'supplier']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_stock');
        Schema::dropIfExists('product_warehouse_stock');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_models');
        Schema::dropIfExists('brands');
    }
};
