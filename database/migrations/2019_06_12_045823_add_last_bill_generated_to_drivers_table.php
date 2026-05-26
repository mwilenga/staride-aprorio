<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastBillGeneratedToDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $columns = [
                'last_bill_generated' => function (Blueprint $table) {
                    $table->string('last_bill_generated')->nullable()->after('merchant_id');
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('drivers', $column)) {
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
        Schema::table('drivers', function (Blueprint $table) {
            $columns = [
                'last_bill_generated',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('drivers', $column)) {
                    $table->dropColumn($column);
                }
            }
});
    }
}
