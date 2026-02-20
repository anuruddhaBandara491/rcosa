<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')
                  ->constrained('members')
                  ->onDelete('cascade');

            $table->decimal('total_amount',   10, 2);   // from .env REGISTRATION_FEE
            $table->decimal('paid_amount',    10, 2)->default(0.00);
            $table->decimal('balance_amount', 10, 2);   // computed: total - paid
            $table->enum('status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->date('payment_date')->nullable();
            $table->string('receipt_number')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['member_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_payments');
    }
};
