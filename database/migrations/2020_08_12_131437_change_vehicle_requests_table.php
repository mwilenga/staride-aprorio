<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeVehicleRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vehicle_requests', function (Blueprint $table) {
            $columns = [
                'user_vehicle_id' => function (Blueprint $table) {
                    $table->unsignedInteger('user_vehicle_id')->nullable();
                },
                'user_id' => function (Blueprint $table) {
                    $table->unsignedInteger('user_id')->nullable();
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('vehicle_requests', $column)) {
                    $callback($table);
                }
            }

            $table->foreign('user_vehicle_id')->references('id')->on('user_vehicles')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
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
