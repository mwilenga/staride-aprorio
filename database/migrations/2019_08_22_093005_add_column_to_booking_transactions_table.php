<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToBookingTransactionsTable extends Migration
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
                'commission_type' => function (Blueprint $table) {
                    $table->tinyInteger('commission_type')->nullable()->after('date_time_details')->comment('1:PrePaid, 2:PostPaid');
                },
                'booking_fee' => function (Blueprint $table) {
                    $table->string('booking_fee')->nullable()->after('toll_amount');
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
            //
        });
    }
}
