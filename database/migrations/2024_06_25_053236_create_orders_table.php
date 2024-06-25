<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_uuid');
            $table->foreign('user_uuid')->references('uuid')->on('users')->onDelete('cascade');
            $table->uuid('order_status_uuid');
            $table->foreign('order_status_uuid')->references('uuid')->on('order_statuses')->onDelete('cascade');
            $table->uuid('payment_uuid');
            $table->foreign('payment_uuid')->references('uuid')->on('payments')->onDelete('cascade');
            $table->uuid('uuid')->unique();
            $table->json('products');
            $table->json('address');
            $table->float('delivery_fee', 8, 2)->nullable();
            $table->float('amount', 8, 2);
            $table->timestamps();
            $table->timestamp('shipping_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
