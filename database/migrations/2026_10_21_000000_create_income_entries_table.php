<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('income_entries')) {
            return;
        }

        Schema::create('income_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('patient_id');
            $table->date('income_date');
            $table->decimal('amount', 10, 2);
            $table->string('payment_mode', 20);
            $table->timestamps();

            $table->index(['income_date', 'payment_mode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('income_entries');
    }
};
