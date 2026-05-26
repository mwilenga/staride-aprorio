<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE = 'business_segments';
    private const COLUMN = 'membership_plan_id';
    private const FK_NAME = 'bs_membership_plan_id_fk';
    private const REF_TABLE = 'merchant_membership_plans';

    public function up(): void
    {
        if (!Schema::hasTable(self::TABLE) || !Schema::hasTable(self::REF_TABLE)) {
            return;
        }

        $this->dropMembershipPlanForeignKeys();

        if (Schema::hasColumn(self::TABLE, self::COLUMN)) {
            DB::statement(
                'ALTER TABLE `' . self::TABLE . '` DROP COLUMN `' . self::COLUMN . '`'
            );
        }

        DB::statement(
            'ALTER TABLE `' . self::TABLE . '` ADD `' . self::COLUMN . '` BIGINT UNSIGNED NULL'
        );

        $exists = DB::selectOne(
            'SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND CONSTRAINT_NAME = ?',
            [self::TABLE, self::FK_NAME]
        );

        if (!$exists) {
            DB::statement(
                'ALTER TABLE `' . self::TABLE . '`
                 ADD CONSTRAINT `' . self::FK_NAME . '`
                 FOREIGN KEY (`' . self::COLUMN . '`)
                 REFERENCES `' . self::REF_TABLE . '` (`id`)
                 ON DELETE CASCADE
                 ON UPDATE RESTRICT'
            );
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable(self::TABLE)) {
            return;
        }

        $this->dropMembershipPlanForeignKeys();
    }

    private function dropMembershipPlanForeignKeys(): void
    {
        $foreignKeys = DB::select(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL',
            [self::TABLE, self::COLUMN]
        );

        foreach ($foreignKeys as $foreignKey) {
            DB::statement(
                'ALTER TABLE `' . self::TABLE . '` DROP FOREIGN KEY `' . $foreignKey->CONSTRAINT_NAME . '`'
            );
        }
    }
};
