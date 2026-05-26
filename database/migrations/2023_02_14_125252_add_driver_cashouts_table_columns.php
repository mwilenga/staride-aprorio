<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddDriverCashoutsTableColumns extends Migration
{
    public function up()
    {
        MigrationSchema::addColumnWithForeign(
            'driver_cashouts',
            'credit_account_detail_id',
            fn (Blueprint $table) => $table->unsignedInteger('credit_account_detail_id')->nullable()->after('merchant_id'),
            'credit_account_details'
        );
    }

    public function down()
    {
        //
    }
}
