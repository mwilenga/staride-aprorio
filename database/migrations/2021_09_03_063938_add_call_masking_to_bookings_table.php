<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCallMaskingToBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $columns = [
                'user_masked_number' => function (Blueprint $table) {
                    $table->string('user_masked_number')->nullable();
                },
                'driver_masked_number' => function (Blueprint $table) {
                    $table->string('driver_masked_number')->nullable();
                },
                'session_sid' => function (Blueprint $table) {
                    $table->string('session_sid')->nullable();
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('bookings', $column)) {
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
        Schema::table('bookings', function (Blueprint $table) {
            //
        });
    }
}
