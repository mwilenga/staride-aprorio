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
        Schema::table('bus_pickup_drop_points', function (Blueprint $table) {
            $columns = [
                'latitude' => function (Blueprint $table) {
                    $table->string('latitude')->nullable()->after("sequence");
                },
                'longitude' => function (Blueprint $table) {
                    $table->string('longitude')->nullable()->after("latitude");
                },
                'address' => function (Blueprint $table) {
                    $table->string('address')->nullable()->after("longitude");
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('bus_pickup_drop_points', $column)) {
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
        Schema::table('bus_pickup_drop_points', function (Blueprint $table) {
            $columns = [
                'latitude',
                'longitude',
                'address',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('bus_pickup_drop_points', $column)) {
                    $table->dropColumn($column);
                }
            }
});
    }
};
