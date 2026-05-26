<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnToCountryAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('country_areas', function (Blueprint $table) {
            $columns = [
                'manual_downgradation' => function (Blueprint $table) {
                    $table->tinyInteger('manual_downgradation')->after('auto_upgradetion')->default('2');
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('country_areas', $column)) {
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
        Schema::table('country_areas', function (Blueprint $table) {
            //
        });
    }
}
