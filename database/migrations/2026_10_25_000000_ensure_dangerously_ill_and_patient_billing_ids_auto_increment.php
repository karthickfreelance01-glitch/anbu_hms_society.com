<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['patients', 'patient_bills', 'patient_billings', 'dangerously_ill'] as $table) {
            $this->ensureIdAutoIncrement($table);
        }
    }

    public function down(): void
    {
        // Keep auto-increment enabled so rolling back other migrations cannot break inserts.
    }

    private function ensureIdAutoIncrement(string $table): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $idColumn = DB::selectOne("SHOW COLUMNS FROM `{$table}` WHERE `Field` = 'id'");
        if (! $idColumn) {
            throw new RuntimeException("The {$table} table does not have an id column.");
        }

        if (str_contains(strtolower($idColumn->Extra), 'auto_increment')) {
            return;
        }

        $columnType = strtoupper($idColumn->Type);
        if (! preg_match('/\A(?:TINYINT|SMALLINT|MEDIUMINT|INT|INTEGER|BIGINT)(?:\(\d+\))?(?:\s+UNSIGNED)?\z/i', $columnType)) {
            throw new RuntimeException("The {$table}.id column must be an integer before it can auto-increment.");
        }

        $uniqueIdIndex = DB::selectOne(
            "SHOW INDEX FROM `{$table}` WHERE `Column_name` = 'id' AND `Non_unique` = 0"
        );

        if (! $uniqueIdIndex) {
            $duplicateId = DB::table($table)
                ->select('id')
                ->groupBy('id')
                ->havingRaw('COUNT(*) > 1')
                ->first();

            if ($duplicateId) {
                throw new RuntimeException(
                    "Cannot safely enable auto-increment for {$table}.id while duplicate IDs exist."
                );
            }

            DB::statement("ALTER TABLE `{$table}` ADD UNIQUE KEY `{$table}_id_unique` (`id`)");
        }

        DB::statement(
            "ALTER TABLE `{$table}` MODIFY `id` {$columnType} NOT NULL AUTO_INCREMENT"
        );
    }
};
