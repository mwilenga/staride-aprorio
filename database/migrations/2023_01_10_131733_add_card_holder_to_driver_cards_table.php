<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCardHolderToDriverCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_cards', function (Blueprint $table) {
            $columns = [
                'card_holder' => function (Blueprint $table) {
                    // $table->string('card_holder')->nullable()->after('token');
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('driver_cards', $column)) {
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
        Schema::table('driver_cards', function (Blueprint $table) {
            //
        });
    }
}
