<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFareNewColumnsToDriverAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_accounts', function (Blueprint $table) {
            $columns = [
                'total_trips_till_now' => function (Blueprint $table) {
                    $table->integer('total_trips_till_now')->default(0)->nullable()->after('total_trips');
                },
                'fare_amount' => function (Blueprint $table) {
                    $table->string('fare_amount')->default(0.0)->nullable()->after('amount');
                },
                'company_commission' => function (Blueprint $table) {
                    $table->string('company_commission')->default(0.0)->nullable()->after('amount');
                },
                'toll_amount' => function (Blueprint $table) {
                    $table->string('toll_amount')->default(0.0)->nullable()->after('amount');
                },
                'tip_amount' => function (Blueprint $table) {
                    $table->string('tip_amount')->default(0.0)->nullable()->after('amount');
                },
                'cancellation_charges' => function (Blueprint $table) {
                    $table->string('cancellation_charges')->default(0.0)->nullable()->after('amount');
                },
                'referral_amount' => function (Blueprint $table) {
                    $table->string('referral_amount')->default(0.0)->nullable()->after('amount');
                },
                'cash_payment_received' => function (Blueprint $table) {
                    $table->string('cash_payment_received')->default(0.0)->nullable()->after('amount');
                },
                'trips_outstanding_sum' => function (Blueprint $table) {
                    $table->string('trips_outstanding_sum')->default(0.0)->nullable()->after('amount');
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('driver_accounts', $column)) {
                    $callback($table);
                }
            }
});
    }

    /**
     * ALTER TABLE `driver_accounts` CHANGE `amount` `amount` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'calculated_amount', CHANGE `settle_type` `settle_type` INT(11) NULL DEFAULT NULL COMMENT '1: Cash 2: NonCash', CHANGE `status` `status` INT(11) NOT NULL DEFAULT '1' COMMENT '1:Generated Only 2:Settled';
     *
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_accounts', function (Blueprint $table) {
            $columns = [
                'total_trips_till_now',
                'fare_amount',
                'company_commission',
                'toll_amount',
                'tip_amount',
                'cancellation_charges',
                'referral_amount',
                'cash_payment_received',
                'trips_outstanding_sum',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('driver_accounts', $column)) {
                    $table->dropColumn($column);
                }
            }
});
    }
}
