<?php

declare(strict_types=1);

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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('external_id')->nullable()->unique();
            $table->string('title');
            $table->text('description');
            $table->string('category', 100)->default('smartphones');
            $table->decimal('price', 10);
            $table->decimal('discount_percentage', 5)->nullable();
            $table->decimal('rating', 3)->nullable();
            $table->integer('stock')->default(0);
            $table->string('brand', 100)->nullable()->index();
            $table->string('sku', 100)->nullable()->unique();
            $table->json('tags')->nullable();
            $table->decimal('weight')->nullable();
            $table->json('dimensions')->nullable();
            $table->string('warranty_information')->nullable();
            $table->string('shipping_information')->nullable();
            $table->string('availability_status', 50)->nullable();
            $table->string('return_policy')->nullable();
            $table->integer('minimum_order_quantity')->nullable();
            $table->json('meta')->nullable();
            $table->json('reviews')->nullable();
            $table->string('thumbnail', 500)->nullable();
            $table->json('images')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
