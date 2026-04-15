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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')->nullable()->constrained('customers', 'id', 'fk_orders_customer')->nullOnDelete();
            $table->dateTime('register_date_time')->useCurrent();
            $table->dateTime('scheduled_time')->nullable();
            $table->enum('state', [
                'pending',
                'ready',
                'completed',
                'canceled'
            ])->default('pending');
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods', 'id', 'fk_orders_payment')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('users', 'id', 'fk_orders_employee')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
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
