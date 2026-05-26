<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToBookingConfigurationsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('booking_configurations', function (Blueprint $table) {
      $columns = [
        'ride_later_cancel_enable_in_cancel_hour' => function (Blueprint $table) {
          $table->unsignedTinyInteger('ride_later_cancel_enable_in_cancel_hour')->nullable();
        },
        'ride_later_cancel_charge_in_cancel_hour' => function (Blueprint $table) {
          $table->unsignedInteger('ride_later_cancel_charge_in_cancel_hour')->nullable();
        },
        'ride_later_payment_types_enable' => function (Blueprint $table) {
          $table->unsignedTinyInteger('ride_later_payment_types_enable')->nullable();
        },
        'ride_later_payment_types' => function (Blueprint $table) {
          $table->string('ride_later_payment_types')->nullable();
        },
        'ride_later_max_num_days' => function (Blueprint $table) {
          $table->unsignedInteger('ride_later_max_num_days')->nullable();
        },
        'driver_cancel_after_time' => function (Blueprint $table) {
          $table->string('driver_cancel_after_time')->nullable();
        },
        'android_user_key' => function (Blueprint $table) {
          $table->string('android_user_key')->nullable();
        },
        'android_driver_key' => function (Blueprint $table) {
          $table->string('android_driver_key')->nullable();
        },
        'ios_user_key' => function (Blueprint $table) {
          $table->string('ios_user_key')->nullable();
        },
        'ios_driver_key' => function (Blueprint $table) {
          $table->string('ios_driver_key')->nullable();
        },
        'ios_map_load_from' => function (Blueprint $table) {
          $table->tinyInteger('ios_map_load_from')->nullable();
        },
        'driver_booking_amount' => function (Blueprint $table) {
          $table->tinyInteger('driver_booking_amount')->nullable();
        },
        'driver_arriving_reminder' => function (Blueprint $table) {
          $table->tinyInteger('driver_arriving_reminder')->nullable();
        },
        'distance_from_app' => function (Blueprint $table) {
          $table->tinyInteger('distance_from_app')->nullable();
        },
        'ride_later_ride_allocation' => function (Blueprint $table) {
          $table->tinyInteger('ride_later_ride_allocation')->default(1)->nullable();
        },
        'manual_dispatch' => function (Blueprint $table) {
          $table->tinyInteger('manual_dispatch')->default(1)->nullable();
        },
        'multiple_destination_price' => function (Blueprint $table) {
          $table->tinyInteger('multiple_destination_price')->nullable();
        },
        'corporate_price_card' => function (Blueprint $table) {
          $table->tinyInteger('corporate_price_card')->nullable();
        },
      ];

      foreach ($columns as $column => $callback) {
        if (!Schema::hasColumn('booking_configurations', $column)) {
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
    Schema::table('booking_configurations', function (Blueprint $table) {
      $columns = [
        'ride_later_cancel_enable_in_cancel_hour',
        'ride_later_cancel_charge_in_cancel_hour',
        'ride_later_payment_types_enable',
        'ride_later_payment_types',
        'ride_later_max_num_days',
        'driver_cancel_after_time',
        'android_user_key',
        'android_driver_key',
        'ios_user_key',
        'ios_driver_key',
        'ios_map_load_from',
        'driver_booking_amount',
        'driver_arriving_reminder',
        'distance_from_app',
        'ride_later_ride_allocation',
        'manual_dispatch',
        'multiple_destination_price',
        'corporate_price_card',
      ];

      foreach ($columns as $column) {
        if (Schema::hasColumn('booking_configurations', $column)) {
          $table->dropColumn($column);
        }
      }
    });
  }
}
