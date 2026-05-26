<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddCurrencyNumericCodeToCountryTable extends Migration
{
    public function up()
    {
        MigrationSchema::recreateColumn(
            'countries',
            'currency_numeric_code',
            fn (Blueprint $table) => $table->integer('currency_numeric_code')->nullable()
        );
    }

    public function down()
    {
        //
    }
}
