<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToBookingCheckoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_checkouts', function (Blueprint $table) {
            $table->foreign('corporate_id')->references('id')->on('corporates')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $columns = [
                'corporate_id' => function (Blueprint $table) {
                    $table->unsignedInteger('corporate_id')->nullable()->after('merchant_id');
                },
                'additional_information' => function (Blueprint $table) {
                    $table->longText('additional_information')->after('additional_notes')->nullable();
                },
                'outstation_ride_type' => function (Blueprint $table) {
                    $table->integer('outstation_ride_type')->nullable();
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('booking_checkouts', $column)) {
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
        Schema::table('booking_checkouts', function (Blueprint $table) {
            //
        });
    }
}
