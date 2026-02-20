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

            $table->unsignedTinyInteger('month');       // 1–12
            $table->unsignedSmallInteger('year');       // e.g. 2024

            $table->decimal('total_amount',   10, 2);   // from .env MONTHLY_FEE
            $table->decimal('paid_amount',    10, 2)->default(0.00);
            $table->decimal('balance_amount', 10, 2);

            $table->enum('status', ['unpaid', 'partial', 'paid'])->default('unpaid');

            $table->date('payment_date')->nullable();
            $table->string('receipt_number')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // One record per member per month+year
            $table->unique(['member_id', 'month', 'year'], 'uniq_member_month_year');
            $table->index(['member_id', 'status']);
            $table->index(['year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_payments');
    }
};
