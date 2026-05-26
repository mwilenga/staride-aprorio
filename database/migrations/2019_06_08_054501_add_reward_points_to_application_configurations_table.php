<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRewardPointsToApplicationConfigurationsTable extends Migration
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
              'reward_points' => function (Blueprint $table) {
                  $table->unsignedTinyInteger('reward_points')->default(0)->nullable();
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
          $columns = [
              'reward_points',
          ];

          foreach ($columns as $column) {
              if (Schema::hasColumn('application_configurations', $column)) {
                  $table->dropColumn($column);
              }
          }
});
    }
}
