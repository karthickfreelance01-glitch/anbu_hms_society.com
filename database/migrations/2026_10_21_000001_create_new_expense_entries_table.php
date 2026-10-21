<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('new_expense_entries')) {
            return;
        }

        Schema::create('new_expense_entries', function (Blueprint $table) {
            $table->id();
            $table->integer('expense_id');
            $table->date('expense_date');
            $table->decimal('amount', 10, 2);
            $table->string('payment_mode', 20);
            $table->timestamps();

            $table->index(['expense_date', 'payment_mode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('new_expense_entries');
    }
};
