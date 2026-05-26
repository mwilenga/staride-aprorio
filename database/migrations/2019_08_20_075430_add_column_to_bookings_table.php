<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddColumnToBookingsTable extends Migration
{
    public function up()
    {
        MigrationSchema::addColumnWithForeign(
            'bookings',
            'corporate_id',
            fn (Blueprint $table) => $table->unsignedInteger('corporate_id')->nullable()->after('merchant_id'),
            'corporates'
        );
    }

    public function down()
    {
        //
    }
}
