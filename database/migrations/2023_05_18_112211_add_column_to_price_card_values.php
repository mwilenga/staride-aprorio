<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToPriceCardValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('price_card_values', function (Blueprint $table) {
            $table->foreign('price_card_slab_id')->on("price_card_slabs")->references("id")->onUpdate('RESTRICT')->onDelete("CASCADE");

            $columns = [
                'price_card_slab_id' => function (Blueprint $table) {
                    $table->unsignedInteger('price_card_slab_id')->nullable()->after('pricing_parameter_id');
                },
                'value_type' => function (Blueprint $table) {
                    $table->tinyInteger('value_type')->nullable()->after('free_value');
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('price_card_values', $column)) {
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
        Schema::table('price_card_values', function (Blueprint $table) {
            //
        });
    }
}
