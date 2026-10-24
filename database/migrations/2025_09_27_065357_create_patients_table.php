<?php

// database/migrations/xxxx_xx_xx_create_patients_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('patients')) {
            $this->ensurePatientIdAutoIncrement();
            return;
        }

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
        if (Schema::hasColumn('patients', 'ip_number')) {
            return;
        }

        Schema::dropIfExists('patients');
    }

    private function ensurePatientIdAutoIncrement(): void
    {
        $idColumn = DB::selectOne("SHOW COLUMNS FROM `patients` WHERE `Field` = 'id'");

        if (!$idColumn) {
            throw new RuntimeException('The existing patients table does not have an id column.');
        }

        if (str_contains(strtolower($idColumn->Extra), 'auto_increment')) {
            return;
        }

        $columnType = strtoupper($idColumn->Type);
        if (!preg_match('/\A(?:TINYINT|SMALLINT|MEDIUMINT|INT|INTEGER|BIGINT)(?:\(\d+\))?(?:\s+UNSIGNED)?\z/i', $columnType)) {
            throw new RuntimeException('The patients.id column must be an integer before it can auto-increment.');
        }

        if (empty($idColumn->Key)) {
            $duplicateId = DB::table('patients')
                ->select('id')
                ->groupBy('id')
                ->havingRaw('COUNT(*) > 1')
                ->first();

            if ($duplicateId) {
                // Re-keying these rows would require remapping existing patient references.
                return;
            }

            $primaryKey = DB::selectOne("SHOW INDEX FROM `patients` WHERE `Key_name` = 'PRIMARY'");
            if ($primaryKey) {
                DB::statement("ALTER TABLE `patients` ADD UNIQUE KEY `patients_id_unique` (`id`)");
            } else {
                DB::statement("ALTER TABLE `patients` ADD PRIMARY KEY (`id`)");
            }
        }

        DB::statement("ALTER TABLE `patients` MODIFY `id` {$columnType} NOT NULL AUTO_INCREMENT");
    }
};
