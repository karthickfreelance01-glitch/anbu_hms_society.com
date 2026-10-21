<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('new_expense_entries') || Schema::hasColumn('new_expense_entries', 'submission_key')) {
            return;
        }

        Schema::table('new_expense_entries', function (Blueprint $table) {
            $table->uuid('submission_key')->nullable()->unique();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('new_expense_entries') || !Schema::hasColumn('new_expense_entries', 'submission_key')) {
            return;
        }

        Schema::table('new_expense_entries', function (Blueprint $table) {
            $table->dropUnique(['submission_key']);
            $table->dropColumn('submission_key');
        });
    }
};
