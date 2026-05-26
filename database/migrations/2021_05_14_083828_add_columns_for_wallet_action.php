<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsForWalletAction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_wallet_transactions', function (Blueprint $table) {
            $table->foreign('action_merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $columns = [
                'action_merchant_id' => function (Blueprint $table) {
                    // $table->unsignedInteger('action_merchant_id')->nullable();
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('user_wallet_transactions', $column)) {
                    $callback($table);
                }
            }
});

        Schema::table('driver_wallet_transactions', function (Blueprint $table) {
            $table->foreign('action_merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $columns = [
                'action_merchant_id' => function (Blueprint $table) {
                    // $table->unsignedInteger('action_merchant_id')->nullable();
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('driver_wallet_transactions', $column)) {
                    $callback($table);
                }
            }
});

        Schema::table('business_segment_wallet_transactions', function (Blueprint $table) {
            $table->foreign('action_merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $columns = [
                'action_merchant_id' => function (Blueprint $table) {
                    // $table->unsignedInteger('action_merchant_id')->nullable();
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('business_segment_wallet_transactions', $column)) {
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
        //
    }
}
