<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_attr_value_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('address_id')->nullable()->constrained()->onDelete('set null');
            $table->string('product_code')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('price', 12, 0)->default(0);
            $table->string('color_code')->nullable();
            $table->decimal('offer_price', 12, 0)->nullable();
            $table->integer('offer_per')->nullable();
            $table->string('status')->default('pending');
            $table->string('ip_address')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
