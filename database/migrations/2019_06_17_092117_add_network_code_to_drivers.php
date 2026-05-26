<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNetworkCodeToDrivers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('drivers')) {
            Schema::table('drivers', function (Blueprint $table) {
                $columns = [
                    'network_code' => function (Blueprint $table) {
                        $table->string('network_code')->nullable();
                    },
                ];

                foreach ($columns as $column => $callback) {
                    if (!Schema::hasColumn('drivers', $column)) {
                        $callback($table);
                    }
                }
});
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $columns = [
                'drivers',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('drivers', $column)) {
                    $table->dropColumn($column);
                }
            }
});
    }
}
