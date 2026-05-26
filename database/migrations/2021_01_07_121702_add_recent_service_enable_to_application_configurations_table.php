<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecentServiceEnableToApplicationConfigurationsTable extends Migration
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
                'recent_services_enable' => function (Blueprint $table) {
                    // $table->tinyInteger('recent_services_enable')->default(1)->comment('1 : enable 2: disable')->nullable();
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
                'recent_services_enable',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('application_configurations', $column)) {
                    $table->dropColumn($column);
                }
            }
});
    }
}
