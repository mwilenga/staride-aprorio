<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('stripe_payouts')) {
            $this->fixExistingTable();

            return;
        }

        Schema::create('stripe_payouts', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_account');
            $table->string('amount');
            $table->string('currency');
            $table->unsignedInteger('merchant_id');
            $table->foreign('merchant_id', 'stripe_payouts_merchant_id_fk')
                ->references('id')
                ->on('merchants')
                ->onUpdate('RESTRICT')
                ->onDelete('CASCADE');
            $table->integer('status')->comment('1=> Pending, 2=> Success, 3=> Faild');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stripe_payouts');
    }

    private function fixExistingTable(): void
    {
        if (!Schema::hasTable('merchants')) {
            return;
        }

        $foreignKeys = DB::select(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL',
            ['stripe_payouts', 'merchant_id']
        );

        foreach ($foreignKeys as $foreignKey) {
            DB::statement(
                'ALTER TABLE `stripe_payouts` DROP FOREIGN KEY `' . $foreignKey->CONSTRAINT_NAME . '`'
            );
        }

        if (Schema::hasColumn('stripe_payouts', 'merchant_id')) {
            DB::statement(
                'ALTER TABLE `stripe_payouts` MODIFY `merchant_id` INT UNSIGNED NOT NULL'
            );
        }

        $exists = DB::selectOne(
            'SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND CONSTRAINT_NAME = ?',
            ['stripe_payouts', 'stripe_payouts_merchant_id_fk']
        );

        if (!$exists) {
            DB::statement(
                'ALTER TABLE `stripe_payouts`
                 ADD CONSTRAINT `stripe_payouts_merchant_id_fk`
                 FOREIGN KEY (`merchant_id`)
                 REFERENCES `merchants` (`id`)
                 ON DELETE CASCADE
                 ON UPDATE RESTRICT'
            );
        }
    }
};
