<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnToDriverWalletTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_wallet_transactions', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('handyman_order_id')->references('id')->on('handyman_orders')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $columns = [
                'order_id' => function (Blueprint $table) {
                    $table->unsignedInteger('order_id')->nullable();
                },
                'handyman_order_id' => function (Blueprint $table) {
                    $table->unsignedInteger('handyman_order_id')->nullable();
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('driver_wallet_transactions', $column)) {
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
        Schema::table('driver_wallet_transactions', function (Blueprint $table) {
            //
        });
    }
}
