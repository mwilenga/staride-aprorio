<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStripeconnectToCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('countries', function (Blueprint $table) {
            $columns = [
                'short_code' => function (Blueprint $table) {
                    $table->string('short_code' , 10)->nullable();
                },
            ];

            foreach ($columns as $column => $callback) {
                if (!Schema::hasColumn('countries', $column)) {
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
        Schema::table('countries', function (Blueprint $table) {
            $columns = [
                'short_code',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('countries', $column)) {
                    $table->dropColumn($column);
                }
            }
});
    }
}
