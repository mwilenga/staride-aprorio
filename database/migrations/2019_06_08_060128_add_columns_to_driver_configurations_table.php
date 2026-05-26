<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToDriverConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_configurations', function (Blueprint $table) {
          $columns = [
              'driver_penalty_enable' => function (Blueprint $table) {
                  $table->unsignedTinyInteger('driver_penalty_enable')->nullable();
              },
              'driver_cancel_count' => function (Blueprint $table) {
                  $table->unsignedInteger('driver_cancel_count')->nullable();
              },
              'driver_penalty_period' => function (Blueprint $table) {
                  $table->unsignedInteger('driver_penalty_period')->nullable();
              },
              'driver_penalty_period_next' => function (Blueprint $table) {
                  $table->unsignedInteger('driver_penalty_period_next')->nullable();
              },
          ];

          foreach ($columns as $column => $callback) {
              if (!Schema::hasColumn('driver_configurations', $column)) {
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
        Schema::table('driver_configurations', function (Blueprint $table) {
          $columns = [
              'driver_penalty_enable',
              'driver_cancel_count',
              'driver_penalty_period',
              'driver_penalty_period_next',
          ];

          foreach ($columns as $column) {
              if (Schema::hasColumn('driver_configurations', $column)) {
                  $table->dropColumn($column);
              }
          }
        });
    }
}
