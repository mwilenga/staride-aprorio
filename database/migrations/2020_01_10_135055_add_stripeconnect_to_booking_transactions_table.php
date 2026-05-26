<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStripeconnectToBookingTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_transactions', function (Blueprint $table) {
            $columns = [
                'instant_settlement' => function (Blueprint $table) {
                    $table->unsignedTinyInteger('instant_settlement')->default(0)->comment('0-no, 1-yes');
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('booking_transactions', $column)) {
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
        Schema::table('booking_transactions', function (Blueprint $table) {
            $columns = [
                'instant_settlement',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('booking_transactions', $column)) {
                    $table->dropColumn($column);
                }
            }
});
    }
}
