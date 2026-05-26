<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
          $columns = [
              'reward_points' => function (Blueprint $table) {
                  $table->double('reward_points')->default(0);
              },
              'usable_reward_points' => function (Blueprint $table) {
                  $table->double('usable_reward_points')->default(0);
              },
              'use_reward_trip_count' => function (Blueprint $table) {
                  $table->unsignedInteger('use_reward_trip_count')->default(0);
              },
              'is_suspended' => function (Blueprint $table) {
                  $table->timestamp('is_suspended')->nullable();
              },
              'rider_gender_choice' => function (Blueprint $table) {
                  $table->tinyInteger('rider_gender_choice')->after('driver_gender')->default(0)->comment('0 :All,1 :Male,2 :Female');
              },
              'website_link' => function (Blueprint $table) {
                  $table->string('website_link')->nullable();
              },
              'business_name' => function (Blueprint $table) {
                  $table->longText('business_name')->nullable();
              },
          ];

          foreach ($columns as $column => $callback) {
              if (!Schema::hasColumn('drivers', $column)) {
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
        Schema::table('drivers', function (Blueprint $table) {
          $columns = [
              'reward_points',
              'usable_reward_points',
              'use_reward_trip_count',
              'is_suspended',
          ];

          foreach ($columns as $column) {
              if (Schema::hasColumn('drivers', $column)) {
                  $table->dropColumn($column);
              }
          }
});
    }
}
