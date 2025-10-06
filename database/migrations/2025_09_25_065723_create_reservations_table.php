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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('reservation_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('deposit', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0); // إجمالي الحجز
            $table->decimal('remaining', 12, 2)->default(0); // المبلغ المتبقي
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->string('description')->nullable();
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
        Schema::dropIfExists('reservations');
    }
};
