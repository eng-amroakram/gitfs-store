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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->morphs('paymentable'); // paymentable_id + paymentable_type
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('payment_reference')->unique();
            $table->decimal('amount', 12, 2);
            $table->enum('method', ['cash', 'card', 'bank_transfer', 'palpay', 'jawwalPay', 'other'])->default('cash');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->timestamp('synced_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
