<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToCorporatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('corporates', function (Blueprint $table) {
            $columns = [
                'wallet_balance' => function (Blueprint $table) {
                    $table->string('wallet_balance')->default('0.0')->after('remember_token');
                },
                'price_type' => function (Blueprint $table) {
                    $table->tinyInteger('price_type')->default(1)->nullable();
                },
                'price_card_amount' => function (Blueprint $table) {
                    $table->string('price_card_amount')->nullable();
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('corporates', $column)) {
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
        Schema::table('corporates', function (Blueprint $table) {
            //
        });
    }
}
