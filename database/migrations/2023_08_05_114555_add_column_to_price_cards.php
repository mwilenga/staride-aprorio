<?php

use App\Support\MigrationSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up()
    {
        MigrationSchema::addColumnWithForeign(
            'price_cards',
            'base_fare_price_card_slab_id',
            fn (Blueprint $table) => $table->unsignedInteger('base_fare_price_card_slab_id')->nullable()->after('free_time'),
            'price_card_slabs'
        );

        MigrationSchema::recreateColumn(
            'price_cards',
            'additional_stop_charges',
            fn (Blueprint $table) => $table->string('additional_stop_charges')->nullable()
        );
    }

    public function down()
    {
        //
    }
};
