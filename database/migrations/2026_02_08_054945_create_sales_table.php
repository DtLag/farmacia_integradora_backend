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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            $table->dateTime('date_time')->useCurrent();
            $table->enum('state', [
                'completed', 
                'canceled',
                'in progress'
            ])->default('in progress');
            $table->decimal('total', 10, 2)->default(0.00);
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods', 'id', 'fk_sales_payment')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users', 'id', 'fk_sales_user')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers', 'id', 'fk_sales_customer')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
