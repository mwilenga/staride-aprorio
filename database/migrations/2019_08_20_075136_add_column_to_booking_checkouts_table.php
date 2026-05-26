<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddColumnToBookingCheckoutsTable extends Migration
{
    public function up()
    {
        MigrationSchema::addColumnWithForeign(
            'booking_checkouts',
            'corporate_id',
            fn (Blueprint $table) => $table->unsignedInteger('corporate_id')->nullable()->after('merchant_id'),
            'corporates'
        );

        MigrationSchema::recreateColumn(
            'booking_checkouts',
            'additional_information',
            fn (Blueprint $table) => $table->longText('additional_information')->after('additional_notes')->nullable()
        );

        MigrationSchema::recreateColumn(
            'booking_checkouts',
            'outstation_ride_type',
            fn (Blueprint $table) => $table->integer('outstation_ride_type')->nullable()
        );
    }

    public function down()
    {
        //
    }
}
