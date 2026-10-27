<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('income_entries') || Schema::hasColumn('income_entries', 'submission_key')) {
            return;
        }

        Schema::table('income_entries', function (Blueprint $table) {
            $table->uuid('submission_key')->nullable()->unique();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('income_entries') || !Schema::hasColumn('income_entries', 'submission_key')) {
            return;
        }

        Schema::table('income_entries', function (Blueprint $table) {
            $table->dropUnique(['submission_key']);
            $table->dropColumn('submission_key');
        });
    }
};
