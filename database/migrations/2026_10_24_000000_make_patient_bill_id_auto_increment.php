<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('patient_bills')) {
            return;
        }

        $idColumn = DB::selectOne("SHOW COLUMNS FROM `patient_bills` WHERE `Field` = 'id'");
        if (!$idColumn) {
            throw new RuntimeException('The patient_bills table does not have an id column.');
        }

        if (str_contains(strtolower($idColumn->Extra), 'auto_increment')) {
            return;
        }

        $columnType = strtoupper($idColumn->Type);
        if (!preg_match('/\A(?:TINYINT|SMALLINT|MEDIUMINT|INT|INTEGER|BIGINT)(?:\(\d+\))?(?:\s+UNSIGNED)?\z/i', $columnType)) {
            throw new RuntimeException('The patient_bills.id column must be an integer before it can auto-increment.');
        }

        $uniqueIdIndex = DB::selectOne(
            "SHOW INDEX FROM `patient_bills` WHERE `Column_name` = 'id' AND `Non_unique` = 0"
        );

        if (!$uniqueIdIndex) {
            $duplicateId = DB::table('patient_bills')
                ->select('id')
                ->groupBy('id')
                ->havingRaw('COUNT(*) > 1')
                ->first();

            if ($duplicateId) {
                $referencingTable = DB::selectOne(
                    "SELECT `TABLE_NAME` FROM `information_schema`.`KEY_COLUMN_USAGE`
                    WHERE `REFERENCED_TABLE_SCHEMA` = DATABASE()
                    AND `REFERENCED_TABLE_NAME` = 'patient_bills'
                    AND `REFERENCED_COLUMN_NAME` = 'id'
                    LIMIT 1"
                );

                if ($referencingTable) {
                    throw new RuntimeException(
                        'Cannot safely regenerate duplicate patient bill IDs because another table references patient_bills.id.'
                    );
                }

                DB::statement(
                    'ALTER TABLE `patient_bills` ADD COLUMN `bill_id_auto_increment` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE FIRST'
                );
                DB::statement(
                    'ALTER TABLE `patient_bills` DROP COLUMN `id`, CHANGE COLUMN `bill_id_auto_increment` `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT'
                );

                return;
            }

            $primaryKey = DB::selectOne(
                "SHOW INDEX FROM `patient_bills` WHERE `Key_name` = 'PRIMARY'"
            );

            if ($primaryKey) {
                DB::statement(
                    'ALTER TABLE `patient_bills` ADD UNIQUE KEY `patient_bills_id_unique` (`id`)'
                );
            } else {
                DB::statement(
                    'ALTER TABLE `patient_bills` ADD PRIMARY KEY (`id`)'
                );
            }
        }

        DB::statement(
            "ALTER TABLE `patient_bills` MODIFY `id` {$columnType} NOT NULL AUTO_INCREMENT"
        );
    }

    public function down(): void
    {
        // Keep auto-increment enabled so rolling back other migrations cannot break bill inserts.
    }
};
