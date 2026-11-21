<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('unit', 20)->default('kg');
            $table->unsignedInteger('price');
            $table->unsignedInteger('quantity_available');
            $table->string('location');
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index(['status', 'category_id']);
            $table->index('price');
        });

        Schema::create('listing_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->timestamps();
        });

        Schema::create('price_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('price');
            $table->timestamp('recorded_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_history');
        Schema::dropIfExists('listing_images');
        Schema::dropIfExists('listings');
        Schema::dropIfExists('categories');
    }
};
