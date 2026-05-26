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
        Schema::table('bus_price_cards', function (Blueprint $table) {
            $columns = [
                'cancel_charges' => function (Blueprint $table) {
                    $table->tinyInteger('cancel_charges')->nullable()->default(2)->after("end_stop_fare");
                },
                'cancel_time' => function (Blueprint $table) {
                    $table->integer('cancel_time')->nullable()->after("cancel_charges");
                },
                'cancel_amount' => function (Blueprint $table) {
                    $table->string('cancel_amount', 191)->nullable()->after("cancel_time");
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('bus_price_cards', $column)) {
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
        Schema::table('bus_price_cards', function (Blueprint $table) {
            $columns = [
                'cancel_charges',
                'cancel_time',
                'cancel_amount',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('bus_price_cards', $column)) {
                    $table->dropColumn($column);
                }
            }
});
    }
};
