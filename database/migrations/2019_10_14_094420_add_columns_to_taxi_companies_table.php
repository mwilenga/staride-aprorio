<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddColumnsToTaxiCompaniesTable extends Migration
{
    public function up()
    {
        MigrationSchema::recreateColumn(
            'taxi_companies',
            'wallet_money',
            fn (Blueprint $table) => $table->string('wallet_money')->nullable()->after('remember_token')
        );

        MigrationSchema::addColumnWithForeign(
            'taxi_companies',
            'segment_id',
            fn (Blueprint $table) => $table->unsignedInteger('segment_id')->after('country_id')->nullable(),
            'segments',
            'RESTRICT',
            'CASCADE'
        );
    }

    public function down()
    {
        //
    }
}
