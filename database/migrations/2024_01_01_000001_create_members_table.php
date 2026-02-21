<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();

            // Optional
            $table->unsignedInteger('membership_number')->nullable()->unique();
            $table->year('school_register_year')->nullable();
            $table->string('admission_number')->nullable();
            $table->date('date_joined_school')->nullable();
            $table->string('email')->nullable();

            // Mandatory
            $table->string('name_with_initials');
            $table->boolean('married')->default(false);
            $table->string('phone_number');
            $table->string('nic_number')->unique();
            $table->date('birthday');
            $table->text('address');
            $table->string('occupation');
            $table->string('current_city');
            $table->enum('gender', ['Male', 'Female']);
            $table->string('district');
            $table->string('election_division');
            $table->string('grama_niladhari_division');

            // JSON optional fields
            $table->json('children_info')->nullable();   // [{name, school}]
            $table->text('siblings_info')->nullable();   // free text if unmarried
            $table->enum('type', ['new', 'existing'])->default('new'); // for future use


            $table->timestamps();
            $table->softDeletes();

            // Indexes for search
            $table->index('name_with_initials');
            $table->index('phone_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
