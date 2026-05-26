<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
          $columns = [
              'reward_points' => function (Blueprint $table) {
                  $table->double('reward_points')->nullable();
              },
              'usable_reward_points' => function (Blueprint $table) {
                  $table->double('usable_reward_points')->nullable();
              },
              'use_reward_trip_count' => function (Blueprint $table) {
                  $table->unsignedInteger('use_reward_trip_count')->default(0);
              },
              'first_reward_pending' => function (Blueprint $table) {
                  $table->unsignedTinyInteger('first_reward_pending')->nullable();
              },
          ];

          foreach ($columns as $column => $callback) {
              if (!Schema::hasColumn('users', $column)) {
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
        Schema::table('users', function (Blueprint $table) {
          $columns = [
              'reward_points',
              'usable_reward_points',
              'use_reward_trip_count',
              'first_reward_pending',
          ];

          foreach ($columns as $column) {
              if (Schema::hasColumn('users', $column)) {
                  $table->dropColumn($column);
              }
          }
});
    }
}
