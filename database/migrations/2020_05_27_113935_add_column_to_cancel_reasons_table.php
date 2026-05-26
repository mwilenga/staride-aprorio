<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddColumnToCancelReasonsTable extends Migration
{
    public function up()
    {
        MigrationSchema::addColumnWithForeign(
            'cancel_reasons',
            'segment_id',
            fn (Blueprint $table) => $table->unsignedInteger('segment_id')->nullable(),
            'segments'
        );
    }

    public function down()
    {
        //
    }
}
