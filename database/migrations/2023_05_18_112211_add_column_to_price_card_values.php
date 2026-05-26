<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddColumnToPriceCardValues extends Migration
{
    public function up()
    {
        MigrationSchema::addColumnWithForeign(
            'price_card_values',
            'price_card_slab_id',
            fn (Blueprint $table) => $table->unsignedInteger('price_card_slab_id')->nullable()->after('pricing_parameter_id'),
            'price_card_slabs'
        );

        MigrationSchema::recreateColumn(
            'price_card_values',
            'value_type',
            fn (Blueprint $table) => $table->tinyInteger('value_type')->nullable()->after('free_value')
        );
    }

    public function down()
    {
        //
    }
}
