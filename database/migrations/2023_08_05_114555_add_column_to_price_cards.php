<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('price_cards', function (Blueprint $table) {
            $table->foreign('base_fare_price_card_slab_id')->on("price_card_slabs")->references("id")->onUpdate('RESTRICT')->onDelete("CASCADE");

            $columns = [
                'base_fare_price_card_slab_id' => function (Blueprint $table) {
                    $table->unsignedInteger('base_fare_price_card_slab_id')->nullable()->after('free_time');
                },
                'additional_stop_charges' => function (Blueprint $table) {
                    $table->string('additional_stop_charges')->nullable();
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('price_cards', $column)) {
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
        Schema::table('price_cards', function (Blueprint $table) {
            //
        });
    }
};
