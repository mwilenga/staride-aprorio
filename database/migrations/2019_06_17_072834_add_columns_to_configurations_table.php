<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $columns = [
                'driver_suspend_penalty_enable' => function (Blueprint $table) {
                    $table->unsignedTinyInteger('driver_suspend_penalty_enable')->nullable();
                },
                'fare_table_based_referral_enable' => function (Blueprint $table) {
                    $table->unsignedTinyInteger('fare_table_based_referral_enable')->nullable();
                },
                'cancel_rate_table_based_cancel_charges_enable' => function (Blueprint $table) {
                    $table->unsignedTinyInteger('cancel_rate_table_based_cancel_charges_enable')->nullable();
                },
                'driver_wallet_withdraw_enable' => function (Blueprint $table) {
                    $table->unsignedTinyInteger('driver_wallet_withdraw_enable')->nullable();
                },
                'ride_later_cancel_in_cancel_hour_enable' => function (Blueprint $table) {
                    $table->unsignedTinyInteger('ride_later_cancel_in_cancel_hour_enable')->nullable();
                },
                'add_money_to_user_wallet_ride_end' => function (Blueprint $table) {
                    $table->tinyInteger('add_money_to_user_wallet_ride_end')->nullable();
                },
                'admin_alert_on_driver_reg' => function (Blueprint $table) {
                    $table->tinyInteger('admin_alert_on_driver_reg')->nullable();
                },
                'handyman_order_start_otp' => function (Blueprint $table) {
                    $table->tinyInteger('handyman_order_start_otp')->default(1)->nullable();
                },
                'bussiness_seg_sub_cat_optional' => function (Blueprint $table) {
                    $table->tinyInteger('bussiness_seg_sub_cat_optional')->nullable();
                },
                'order_id_verification' => function (Blueprint $table) {
                    $table->tinyInteger('order_id_verification')->nullable();
                },
                'new_ride_before_ride_end' => function (Blueprint $table) {
                    $table->tinyInteger('new_ride_before_ride_end')->nullable();
                },
                'referral_autofill' => function (Blueprint $table) {
                    $table->tinyInteger('referral_autofill')->nullable();
                },
                'handyman_manual_bidding' => function (Blueprint $table) {
                    $table->tinyInteger('handyman_manual_bidding')->nullable();
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('configurations', $column)) {
                    $callback($table);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $columns = [
                'driver_suspend_penalty_enable',
                'fare_table_based_referral_enable',
                'cancel_rate_table_based_cancel_charges_enable',
                'driver_wallet_withdraw_enable',
                'ride_later_cancel_in_cancel_hour_enable',
                'add_money_to_user_wallet_ride_end',
                'admin_alert_on_driver_reg',
                'handyman_order_start_otp',
                'bussiness_seg_sub_cat_optional',
                'order_id_verification',
                'new_ride_before_ride_end',
                'referral_autofill',
                'handyman_manual_bidding',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('configurations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
