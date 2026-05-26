<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('corporate_id')->references('id')->on('corporates')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $columns = [
                'corporate_id' => function (Blueprint $table) {
                    $table->unsignedInteger('corporate_id')->nullable()->after('merchant_booking_id');
                },
                'additional_information' => function (Blueprint $table) {
                    $table->longText('additional_information')->after('additional_notes')->nullable();
                },
                'additional_movers' => function (Blueprint $table) {
                    $table->integer('additional_movers')->nullable()->after('additional_information');
                },
                'receiver_details' => function (Blueprint $table) {
                    $table->string('receiver_details')->nullable()->after('additional_movers');
                },
                'outstation_ride_type' => function (Blueprint $table) {
                    $table->integer('outstation_ride_type')->nullable();
                },
                'product_images' => function (Blueprint $table) {
                    // $table->string('product_images')->nullable()->after('receiver_details');
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('bookings', $column)) {
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
        Schema::table('bookings', function (Blueprint $table) {
            //
        });
    }
}
