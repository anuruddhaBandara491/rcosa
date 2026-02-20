<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('member_id')
                  ->constrained('members')
                  ->onDelete('cascade');

            $table->string('reason');                       // Reason for donation
            $table->decimal('amount', 12, 2);              // Donation amount
            $table->date('donation_date');                  // Date of donation
            $table->string('receipt_number')->nullable();   // Optional receipt
            $table->text('notes')->nullable();              // Additional notes
            $table->enum('status', ['received', 'pending'])->default('received');

            $table->timestamps();
            $table->softDeletes();

            // Indexes for filtering
            $table->index(['member_id']);
            $table->index(['donation_date']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
