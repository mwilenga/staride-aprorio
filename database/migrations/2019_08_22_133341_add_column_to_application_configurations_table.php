<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToApplicationConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('application_configurations', function (Blueprint $table) {
            $columns = [
                'add_wallet_money_signup' => function (Blueprint $table) {
                    $table->integer('add_wallet_money_signup')->nullable()->after('gender')->default(2)->comment('1 : Enable,2 : Disable');
                },
                'tip_short_amount' => function (Blueprint $table) {
                    $table->string('tip_short_amount')->nullable();
                },
                'instant_grocery_delivery' => function (Blueprint $table) {
                    $table->tinyInteger('instant_grocery_delivery')->nullable();
                },
                'default_otp' => function (Blueprint $table) {
                    $table->tinyInteger('default_otp')->nullable();
                },
                'mileage_reward' => function (Blueprint $table) {
                    $table->tinyInteger('mileage_reward')->nullable();
                },
                'app_auto_cashout' => function (Blueprint $table) {
                    $table->tinyInteger('app_auto_cashout')->nullable();
                },
                'show_horizontal_services' => function (Blueprint $table) {
                    $table->tinyInteger('show_horizontal_services')->nullable();
                },
                'show_single_segment_home_screen' => function (Blueprint $table) {
                    $table->tinyInteger('show_single_segment_home_screen')->nullable();
                },
                'app_loading_bar' => function (Blueprint $table) {
                    $table->string('app_loading_bar')->nullable();
                },
                'rate_us_user_driver' => function (Blueprint $table) {
                    $table->tinyInteger('rate_us_user_driver')->nullable();
                },
                'business_name_on_signup' => function (Blueprint $table) {
                    $table->tinyInteger('business_name_on_signup')->nullable();
                },
                'cell_layout' => function (Blueprint $table) {
                    $table->tinyInteger('cell_layout')->default(1)->nullable();
                },
                'time_slot_unavail_popup' => function (Blueprint $table) {
                    $table->tinyInteger('time_slot_unavail_popup')->nullable();
                },
                'driver_cashout_dynamic' => function (Blueprint $table) {
                    $table->tinyInteger('driver_cashout_dynamic')->nullable()->default(1)->comment('1 : Enable,2 : Disable');
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('application_configurations', $column)) {
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
        Schema::table('application_configurations', function (Blueprint $table) {
            //
        });
    }
}
