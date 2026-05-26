<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnOnDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {

            $table->foreign('driver_agency_id')->references('id')->on('driver_agencies')->onUpdate('RESTRICT')->onDelete('CASCADE');


            $columns = [

                'driver_agency_id' => function (Blueprint $table) {

                    $table->integer('driver_agency_id')->unsigned()->nullable();

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
     *;
     * @return void
     */
    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            //
//            $table->dropForeign('merchant_id');
        });
    }
}
