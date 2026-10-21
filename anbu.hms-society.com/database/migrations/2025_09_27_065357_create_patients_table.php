<?php

// database/migrations/xxxx_xx_xx_create_patients_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('reg_no')->unique();
            $table->date('date_of_admission');
            $table->time('time_of_admission');
            $table->string('name');
            $table->integer('age');
            $table->enum('gender',['Male','Female','Other']);
            $table->enum('marital_status',['Single','Married','Widowed','Divorced'])->nullable();
            $table->text('present_address');
            $table->text('permanent_address')->nullable();
            $table->string('admitted_by')->nullable();
            $table->string('contact_number');
            $table->string('occupation')->nullable();
            $table->string('referral_source')->nullable();
            $table->date('date_of_discharge')->nullable();
            $table->string('aadhar_number')->nullable();
            $table->string('photo')->nullable();
            $table->decimal('registration_fees',10,2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('patients');
    }
};
