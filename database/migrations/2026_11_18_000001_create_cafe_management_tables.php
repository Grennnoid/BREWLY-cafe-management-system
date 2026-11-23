<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations based on the ERD specifications.
     */
    public function up(): void
    {
        // 1. CATEGORY
        Schema::create('categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. MENU
        Schema::create('menus', function (Blueprint $table) {
            $table->id('menu_id');
            $table->unsignedBigInteger('category_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('image_url')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->foreign('category_id')->references('category_id')->on('categories')->onDelete('cascade');
        });

        // 3. CAFE_TABLE
        Schema::create('cafe_tables', function (Blueprint $table) {
            $table->id('table_id');
            $table->string('table_number')->unique();
            $table->integer('capacity')->default(2);
            $table->enum('status', ['AVAILABLE', 'OCCUPIED'])->default('AVAILABLE');
            $table->timestamps();
        });

        // 4. CART
        Schema::create('carts', function (Blueprint $table) {
            $table->id('cart_id');
            $table->string('session_id')->unique();
            $table->timestamps();
        });

        // 5. CART_ITEM
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id('cart_item_id');
            $table->unsignedBigInteger('cart_id');
            $table->unsignedBigInteger('menu_id');
            $table->integer('quantity')->default(1);
            $table->string('note')->nullable();
            $table->timestamps();

            $table->foreign('cart_id')->references('cart_id')->on('carts')->onDelete('cascade');
            $table->foreign('menu_id')->references('menu_id')->on('menus')->onDelete('cascade');
        });

        // 6. STAFF
        Schema::create('staff', function (Blueprint $table) {
            $table->id('staff_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password_hash');
            $table->enum('role', ['ADMIN', 'CASHIER', 'KITCHEN']);
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        // 7. ORDERS
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->string('customer_name')->nullable();
            $table->unsignedBigInteger('table_id')->nullable();
            $table->enum('order_type', ['DINE_IN', 'TAKE_AWAY'])->default('DINE_IN');
            $table->enum('status', ['PENDING', 'ACCEPTED', 'PREPARING', 'READY', 'COMPLETED', 'CANCELLED'])->default('PENDING');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->text('note')->nullable();
            $table->dateTime('ordered_at');
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('table_id')->references('table_id')->on('cafe_tables')->onDelete('set null');
        });

        // 8. ORDER_ITEM
        Schema::create('order_items', function (Blueprint $table) {
            $table->id('order_item_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('menu_id');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 12, 2);
            $table->string('note')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
            $table->foreign('menu_id')->references('menu_id')->on('menus')->onDelete('cascade');
        });

        // 9. ORDER_STATUS_LOG
        Schema::create('order_status_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->dateTime('changed_at');
            $table->timestamps();

            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
            $table->foreign('changed_by')->references('staff_id')->on('staff')->onDelete('set null');
        });

        // 10. PAYMENT
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('confirmed_by')->nullable();
            $table->enum('method', ['CASH', 'QRIS', 'TRANSFER']);
            $table->enum('status', ['PENDING', 'PAID', 'FAILED'])->default('PENDING');
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('change_amount', 12, 2)->default(0);
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
            $table->foreign('confirmed_by')->references('staff_id')->on('staff')->onDelete('set null');
        });

        // 11. RECEIPT
        Schema::create('receipts', function (Blueprint $table) {
            $table->id('receipt_id');
            $table->unsignedBigInteger('payment_id');
            $table->unsignedBigInteger('issued_by');
            $table->string('receipt_number')->unique();
            $table->dateTime('issued_at');
            $table->timestamps();

            $table->foreign('payment_id')->references('payment_id')->on('payments')->onDelete('cascade');
            $table->foreign('issued_by')->references('staff_id')->on('staff')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_status_logs');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('staff');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('cafe_tables');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('categories');
    }
};

