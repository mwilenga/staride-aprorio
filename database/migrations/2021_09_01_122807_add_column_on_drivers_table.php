<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddColumnOnDriversTable extends Migration
{
    public function up()
    {
        MigrationSchema::addColumnWithForeign(
            'drivers',
            'driver_agency_id',
            fn (Blueprint $table) => $table->unsignedInteger('driver_agency_id')->nullable(),
            'driver_agencies'
        );
    }

    public function down()
    {
        //
    }
}
