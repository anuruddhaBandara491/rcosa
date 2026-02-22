<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
      public function up(): void
    {
        Schema::create('monthly_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('member_id')
                  ->constrained('members')
                  ->onDelete('cascade');

            // The actual amount handed over in this single transaction
            $table->decimal('paid_amount',      10, 2);

            // Total fee owed by the member at time of payment (months × fee)
            $table->decimal('total_due',        10, 2);

            // Running total of all payments for this member up to this row
            $table->decimal('cumulative_paid',  10, 2);

            // total_due − cumulative_paid  (negative means overpaid)
            $table->decimal('balance_amount',   10, 2);

            // partial | paid | overpaid
            $table->enum('status', ['partial', 'paid', 'overpaid'])->default('partial');

            // JSON array of months this payment covers, e.g.:
            // [{"month":4,"year":2025,"label":"April 2025","amount":1000},...]
            $table->json('months_covered')->nullable();

            $table->date('payment_date');
            $table->string('receipt_number', 100)->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['member_id', 'status']);
            $table->index('payment_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_payments');
    }
};
